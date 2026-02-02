<?php

namespace App\Filament\Resources\TaxCutResource\Pages;

use App\Filament\Resources\TaxCutResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTaxCut extends CreateRecord
{
    protected static string $resource = TaxCutResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
