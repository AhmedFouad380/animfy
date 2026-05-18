<?php

namespace App\Filament\Resources\ThreeDObjectResource\Pages;

use App\Filament\Resources\ThreeDObjectResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateThreeDObject extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = ThreeDObjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
