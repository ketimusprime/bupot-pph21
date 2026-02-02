<?php

namespace App\Filament\Resources\PartTimerResource\Pages;

use App\Filament\Resources\PartTimerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartTimers extends ListRecords
{
    protected static string $resource = PartTimerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
