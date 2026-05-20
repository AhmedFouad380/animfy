<?php

namespace App\Filament\Resources\LessonResource\Pages;

use App\Filament\Resources\LessonResource;
use App\Filament\Traits\TranslatableCreatePage;
use Filament\Resources\Pages\CreateRecord;

class CreateLesson extends CreateRecord
{
    use TranslatableCreatePage;

    protected static string $resource = LessonResource::class;
}
