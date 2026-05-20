<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Filament\Traits\TranslatableCreatePage;
use Filament\Resources\Pages\CreateRecord;

class CreateCourse extends CreateRecord
{
    use TranslatableCreatePage;

    protected static string $resource = CourseResource::class;
}
