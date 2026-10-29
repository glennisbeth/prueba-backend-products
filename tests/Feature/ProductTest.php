<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());
    }

    public function test_it_lists_products(): void
    {
        Product::factory()->count(3)->create();

        $this->getJson('/api/products')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_it_creates_a_product(): void
    {
        $currency = Currency::factory()->create();

        $payload = [
            'name' => 'Mechanical keyboard',
            'description' => 'Red switches and backlight',
            'price' => 120.50,
            'currency_id' => $currency->id,
            'tax_cost' => 22.90,
            'manufacturing_cost' => 60,
        ];

        $this->postJson('/api/products', $payload)
            ->assertCreated()
            ->assertJsonPath('data.name', 'Mechanical keyboard')
            ->assertJsonPath('data.price', '120.50')
            ->assertJsonPath('data.currency.id', $currency->id);

        $this->assertDatabaseHas('products', ['name' => 'Mechanical keyboard']);
    }

    public function test_it_validates_required_fields(): void
    {
        $this->postJson('/api/products', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'price', 'currency_id', 'tax_cost', 'manufacturing_cost']);
    }

    public function test_it_rejects_negative_amounts(): void
    {
        $currency = Currency::factory()->create();

        $this->postJson('/api/products', [
            'name' => 'Product',
            'price' => -10,
            'currency_id' => $currency->id,
            'tax_cost' => 0,
            'manufacturing_cost' => 0,
        ])->assertJsonValidationErrors('price');
    }

    public function test_it_shows_a_product(): void
    {
        $product = Product::factory()->create();

        $this->getJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_it_returns_404_for_a_missing_product(): void
    {
        $this->getJson('/api/products/999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');
    }

    public function test_it_updates_a_product(): void
    {
        $product = Product::factory()->create();

        $this->putJson("/api/products/{$product->id}", ['name' => 'Updated name'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated name');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated name',
        ]);
    }

    public function test_it_deletes_a_product(): void
    {
        $product = Product::factory()->create();

        $this->deleteJson("/api/products/{$product->id}")->assertNoContent();

        $this->assertSoftDeleted($product);
        $this->getJson("/api/products/{$product->id}")->assertNotFound();
    }
}