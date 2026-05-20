<?php

namespace App\Filament\Resources\ThreeDObjectResource\Pages;

use App\Filament\Resources\ThreeDObjectResource;
use App\Filament\Traits\TranslatableEditPage;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditThreeDObject extends EditRecord
{
    use TranslatableEditPage;

    protected static string $resource = ThreeDObjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
