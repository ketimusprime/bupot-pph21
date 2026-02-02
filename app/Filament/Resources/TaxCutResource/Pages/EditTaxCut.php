<?php

namespace App\Filament\Resources\TaxCutResource\Pages;

use App\Filament\Resources\TaxCutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaxCut extends EditRecord
{
    protected static string $resource = TaxCutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
