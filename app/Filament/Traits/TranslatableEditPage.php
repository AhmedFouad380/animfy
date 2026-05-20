<?php

namespace App\Filament\Traits;

trait TranslatableEditPage
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        foreach ($record->getTranslatableAttributes() as $attribute) {
            $data[$attribute] = $record->getTranslation($attribute, 'en');
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();
        foreach ($record->getTranslatableAttributes() as $attribute) {
            if (array_key_exists($attribute, $data)) {
                $translations = $record->getTranslations($attribute);
                $translations['en'] = $data[$attribute];
                $data[$attribute] = $translations;
            }
        }
        return $data;
    }
}
