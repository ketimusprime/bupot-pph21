<?php

namespace App\Filament\Resources\TaxCutResource\Pages;

use App\Filament\Resources\TaxCutResource;
use App\Models\TaxCut;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TaxCutsExport;
use Illuminate\Database\Eloquent\Builder;

class ListTaxCuts extends ListRecords
{
    protected static string $resource = TaxCutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return Excel::download(new TaxCutsExport($this->getFilteredTableQuery()), 'bukti-potong-pph21-' . date('Y-m-d') . '.xlsx');
                })
                ->color('success'),
            Actions\CreateAction::make(),
        ];
    }
    
    public function getFilteredTableQuery(): Builder
    {
        return $this->getTableQuery();
    }
}
