<?php

namespace App\Services;

use App\Models\Currency;
use InvalidArgumentException;

class CurrencyConverter
{
    /*
     * Converts an amount between two currencies using each currency's
     * exchange rate against the reference currency (exchange_rate = 1).
     */
    public function convert(float|string $amount, Currency $from, Currency $to): float
    {
        if ($from->is($to)) {
            return round((float) $amount, 2);
        }

        if ((float) $from->exchange_rate <= 0) {
            throw new InvalidArgumentException("Currency {$from->name} does not have a valid exchange rate.");
        }

        $amountInReference = (float) $amount / (float) $from->exchange_rate;

        return round($amountInReference * (float) $to->exchange_rate, 2);
    }
}