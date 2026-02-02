<?php

namespace App\Services;

class TaxCutCalculator
{
    public static function calculate(
        float $commission,
        float $taxRate,
        string $method
    ): array {
        $commission = max($commission, 0);
        $taxRate    = max($taxRate, 0);

        $dpp = $commission * 0.5;
        $tax = $dpp * ($taxRate / 100);

        $net = match ($method) {
            'gross_up' => $commission,
            default    => $commission - $tax,
        };

        return [
            'commission_amount' => round($commission),
            'dpp_amount'        => round($dpp),
            'tax_amount'        => round($tax),
            'net_payment'       => round($net),
        ];
    }
}
