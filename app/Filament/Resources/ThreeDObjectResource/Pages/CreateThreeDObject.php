<?php

namespace App\Filament\Resources\ThreeDObjectResource\Pages;

use App\Filament\Resources\ThreeDObjectResource;
use App\Filament\Traits\TranslatableCreatePage;
use Filament\Resources\Pages\CreateRecord;

class CreateThreeDObject extends CreateRecord
{
    use TranslatableCreatePage;

    protected static string $resource = ThreeDObjectResource::class;
}
