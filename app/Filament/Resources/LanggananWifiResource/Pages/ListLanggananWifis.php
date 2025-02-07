<?php

namespace App\Filament\Resources\LanggananWifiResource\Pages;

use App\Filament\Resources\LanggananWifiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLanggananWifis extends ListRecords
{
    protected static string $resource = LanggananWifiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
