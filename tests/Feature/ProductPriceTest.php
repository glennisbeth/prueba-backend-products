<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductPriceTest extends TestCase
{
    use RefreshDatabase;

    private Currency $usd;
    private Currency $eur;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());

        $this->usd = Currency::factory()->create(['name' => 'USD', 'exchange_rate' => 1]);
        $this->eur = Currency::factory()->create(['name' => 'EUR', 'exchange_rate' => 0.92]);

        $this->product = Product::factory()->create([
            'price' => 100,
            'currency_id' => $this->usd->id,
        ]);
    }

    public function test_it_registers_a_price_with_an_explicit_amount(): void
    {
        $this->postJson("/api/products/{$this->product->id}/prices", [
            'currency_id' => $this->eur->id,
            'price' => 95.5,
        ])
            ->assertCreated()
            ->assertJsonPath('data.price', '95.50')
            ->assertJsonPath('data.currency.name', 'EUR');
    }

    public function test_it_calculates_the_price_when_it_is_not_sent(): void
    {
        $this->postJson("/api/products/{$this->product->id}/prices", [
            'currency_id' => $this->eur->id,
        ])
            ->assertCreated()
            ->assertJsonPath('data.price', '92.00');
    }

    public function test_it_does_not_allow_duplicated_currencies(): void
    {
        $this->product->prices()->create(['currency_id' => $this->eur->id, 'price' => 90]);

        $this->postJson("/api/products/{$this->product->id}/prices", [
            'currency_id' => $this->eur->id,
            'price' => 91,
        ])->assertJsonValidationErrors('currency_id');
    }

    public function test_it_does_not_allow_the_base_currency(): void
    {
        $this->postJson("/api/products/{$this->product->id}/prices", [
            'currency_id' => $this->usd->id,
            'price' => 100,
        ])->assertJsonValidationErrors('currency_id');
    }

    public function test_it_lists_the_prices_of_a_product(): void
    {
        $this->product->prices()->create(['currency_id' => $this->eur->id, 'price' => 92]);

        $this->getJson("/api/products/{$this->product->id}/prices")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}