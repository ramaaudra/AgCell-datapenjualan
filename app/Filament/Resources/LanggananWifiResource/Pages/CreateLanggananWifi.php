<?php

namespace App\Filament\Resources\LanggananWifiResource\Pages;

use App\Filament\Resources\LanggananWifiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLanggananWifi extends CreateRecord
{
    protected static string $resource = LanggananWifiResource::class;

     //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
