<?php

namespace App\Filament\Widgets;

use App\Models\TaxCut;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class RecentTaxCuts extends BaseWidget
{
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()->can('taxcut.view');
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        return TaxCut::query()
            ->latest()
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('memo_number')
                ->label('Memo'),

            Tables\Columns\TextColumn::make('company.name')
                ->label('Perusahaan'),

            Tables\Columns\TextColumn::make('net_payment')
                ->money('IDR'),
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
