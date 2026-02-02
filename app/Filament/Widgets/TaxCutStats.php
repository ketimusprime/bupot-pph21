<?php

namespace App\Filament\Widgets;

use App\Models\TaxCut;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TaxCutStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        if (! auth()->user()->can('taxcut.view')) {
            return [];
        }

        return [
            Stat::make(
                'Total Bukti Potong',
                TaxCut::count()
            )
                ->description('Semua data')
                ->icon('heroicon-o-document-text'),

            Stat::make(
                'Total PPh 21',
                'Rp ' . number_format(
                    TaxCut::sum('tax_amount'),
                    0,
                    ',',
                    '.'
                )
            )
                ->icon('heroicon-o-banknotes'),

            Stat::make(
                'Total Dibayarkan',
                'Rp ' . number_format(
                    TaxCut::sum('net_payment'),
                    0,
                    ',',
                    '.'
                )
            )
                ->icon('heroicon-o-currency-dollar'),
        ];
    }

    public function getColumns(): int
    {
        return 3;
    }

    public function getColumnSpan(): int|array
    {
        return [
            'default' => 12,
            'lg' => 12,
        ];
    }
}
