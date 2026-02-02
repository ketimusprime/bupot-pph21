<?php

namespace App\Filament\Widgets;

use App\Models\TaxCut;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TaxCutThisMonth extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        if (! auth()->user()->hasAnyRole(['admin', 'accounting'])) {
            return [];
        }

        $start = now()->startOfMonth();
        $end   = now()->endOfMonth();

        return [
            Stat::make(
                'Bukti Potong Bulan Ini',
                TaxCut::whereBetween('cut_date', [$start, $end])->count()
            ),

            Stat::make(
                'PPh Bulan Ini',
                'Rp ' . number_format(
                    TaxCut::whereBetween('cut_date', [$start, $end])->sum('tax_amount'),
                    0, ',', '.'
                )
            ),
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

