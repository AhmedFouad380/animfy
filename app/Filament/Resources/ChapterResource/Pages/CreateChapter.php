<?php

namespace App\Filament\Resources\ChapterResource\Pages;

use App\Filament\Resources\ChapterResource;
use App\Filament\Traits\TranslatableCreatePage;
use Filament\Resources\Pages\CreateRecord;

class CreateChapter extends CreateRecord
{
    use TranslatableCreatePage;

    protected static string $resource = ChapterResource::class;
}
