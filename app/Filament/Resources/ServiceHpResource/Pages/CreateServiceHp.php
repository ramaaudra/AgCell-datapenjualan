<?php

namespace App\Filament\Resources\ServiceHpResource\Pages;

use App\Filament\Resources\ServiceHpResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceHp extends CreateRecord
{
    protected static string $resource = ServiceHpResource::class;

     //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
