<?php

namespace App\Filament\Resources\ServiceHpResource\Pages;

use App\Filament\Resources\ServiceHpResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceHp extends EditRecord
{
    protected static string $resource = ServiceHpResource::class;

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
