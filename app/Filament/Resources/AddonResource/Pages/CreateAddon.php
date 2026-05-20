<?php

namespace App\Filament\Resources\AddonResource\Pages;

use App\Filament\Resources\AddonResource;
use App\Filament\Traits\TranslatableCreatePage;
use Filament\Resources\Pages\CreateRecord;

class CreateAddon extends CreateRecord
{
    use TranslatableCreatePage;

    protected static string $resource = AddonResource::class;
}
