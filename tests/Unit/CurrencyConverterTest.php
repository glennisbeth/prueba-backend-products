<?php

namespace Tests\Unit;

use App\Models\Currency;
use App\Services\CurrencyConverter;
use InvalidArgumentException;
use Tests\TestCase;

class CurrencyConverterTest extends TestCase
{
    private CurrencyConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->converter = new CurrencyConverter();
    }

    public function test_it_converts_from_the_reference_currency(): void
    {
        $usd = $this->currency(1, 'USD', 1);
        $eur = $this->currency(2, 'EUR', 0.92);

        $this->assertEquals(92.0, $this->converter->convert(100, $usd, $eur));
    }

    public function test_it_converts_between_two_non_reference_currencies(): void
    {
        $cop = $this->currency(1, 'COP', 4100);
        $eur = $this->currency(2, 'EUR', 0.92);

        $this->assertEquals(92.0, $this->converter->convert(410000, $cop, $eur));
    }

    public function test_it_returns_the_same_amount_for_the_same_currency(): void
    {
        $usd = $this->currency(1, 'USD', 1);

        $this->assertEquals(120.5, $this->converter->convert('120.50', $usd, $usd));
    }

    public function test_it_fails_with_an_invalid_exchange_rate(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $broken = $this->currency(1, 'XXX', 0);
        $eur = $this->currency(2, 'EUR', 0.92);

        $this->converter->convert(100, $broken, $eur);
    }

    private function currency(int $id, string $name, float $rate): Currency
    {
        $currency = new Currency(['name' => $name, 'symbol' => '$', 'exchange_rate' => $rate]);
        $currency->id = $id;

        return $currency;
    }
}