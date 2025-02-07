<?php

namespace App\Filament\Resources\ServiceHpResource\Pages;

use App\Filament\Resources\ServiceHpResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServiceHps extends ListRecords
{
    protected static string $resource = ServiceHpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
