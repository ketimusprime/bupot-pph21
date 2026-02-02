<?php

namespace App\Filament\Widgets;

use App\Models\TaxCut;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class TaxCutByCompany extends Widget
{
    protected static string $view = 'filament.widgets.tax-cut-by-company';

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    protected function getViewData(): array
    {
        return [
            'data' => TaxCut::select(
                DB::raw('companies.name as company'),
                DB::raw('COUNT(tax_cuts.id) as total')
            )
            ->join('companies', 'companies.id', '=', 'tax_cuts.company_id')
            ->groupBy('companies.name')
            ->get(),
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}
