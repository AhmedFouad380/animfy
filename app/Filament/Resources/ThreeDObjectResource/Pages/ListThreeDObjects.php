<?php

namespace App\Filament\Resources\ThreeDObjectResource\Pages;

use App\Filament\Resources\ThreeDObjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListThreeDObjects extends ListRecords
{
    protected static string $resource = ThreeDObjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
