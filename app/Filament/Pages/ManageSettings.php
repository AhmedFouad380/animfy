<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.manage-settings';

    protected static ?string $navigationLabel = 'Paymob Settings';

    protected static ?string $title = 'Paymob Settings';

    protected static ?string $navigationGroup = 'Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'paymob_api_key' => Setting::get('paymob_api_key'),
            'paymob_integration_id' => Setting::get('paymob_integration_id'),
            'paymob_iframe_id' => Setting::get('paymob_iframe_id'),
            'paymob_hmac_secret' => Setting::get('paymob_hmac_secret'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Paymob Payment Gateway')
                    ->description('Configure Paymob API credentials, Integration IDs, and security details to dynamically accept online payments.')
                    ->schema([
                        TextInput::make('paymob_api_key')
                            ->label('API Key')
                            ->placeholder('Enter your Paymob API key from settings')
                            ->password()
                            ->revealable()
                            ->required(),
                            
                        TextInput::make('paymob_integration_id')
                            ->label('Integration ID')
                            ->placeholder('Credit Card or Mobile Wallet Integration ID')
                            ->numeric()
                            ->required(),
                            
                        TextInput::make('paymob_iframe_id')
                            ->label('Iframe ID')
                            ->placeholder('Acceptance Iframe ID')
                            ->numeric()
                            ->required(),
                            
                        TextInput::make('paymob_hmac_secret')
                            ->label('HMAC Secret')
                            ->placeholder('Webhook HMAC signature secret')
                            ->password()
                            ->revealable()
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            foreach ($data as $key => $value) {
                Setting::set($key, $value);
            }

            Notification::make()
                ->success()
                ->title('Settings saved successfully!')
                ->send();
        } catch (\Exception $exception) {
            Notification::make()
                ->danger()
                ->title('Error saving settings')
                ->body($exception->getMessage())
                ->send();
        }
    }
}
