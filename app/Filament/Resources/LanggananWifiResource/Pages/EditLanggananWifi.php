<?php

namespace App\Filament\Resources\LanggananWifiResource\Pages;

use App\Filament\Resources\LanggananWifiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLanggananWifi extends EditRecord
{
    protected static string $resource = LanggananWifiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
