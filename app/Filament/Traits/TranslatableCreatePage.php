<?php

namespace App\Filament\Traits;

trait TranslatableCreatePage
{
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $model = $this->getModel();
        $dummy = new $model;
        foreach ($dummy->getTranslatableAttributes() as $attribute) {
            if (array_key_exists($attribute, $data)) {
                $data[$attribute] = [
                    'en' => $data[$attribute],
                    'ar' => $data[$attribute],
                ];
            }
        }
        return $data;
    }
}
