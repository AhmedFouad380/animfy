<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
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
            'instapay_address' => Setting::get('instapay_address'),
            'instapay_qr_code' => Setting::get('instapay_qr_code'),
            'instapay_steps_ar' => Setting::get('instapay_steps_ar'),
            'instapay_steps_en' => Setting::get('instapay_steps_en'),
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

                Section::make('InstaPay Configuration')
                    ->description('Configure InstaPay payment details for students to transfer manually.')
                    ->schema([
                        TextInput::make('instapay_address')
                            ->label('InstaPay Address (IPA or Phone)')
                            ->placeholder('e.g. username@instapay or mobile phone number')
                            ->required(),

                        FileUpload::make('instapay_qr_code')
                            ->label('InstaPay QR Code Image')
                            ->image()
                            ->directory('settings')
                            ->helperText('Upload a custom InstaPay QR code image. If left blank, it will fallback to the default image in public folder.'),

                        RichEditor::make('instapay_steps_ar')
                            ->label('Transfer Steps (Arabic)')
                            ->helperText('أدخل خطوات التحويل للطالب باللغة العربية. استخدم الكلمة المفتاحية :price لعرض السعر ديناميكياً (مثال: "تأكد من تحويل :price جنيه مصري").')
                            ->required(),

                        RichEditor::make('instapay_steps_en')
                            ->label('Transfer Steps (English)')
                            ->helperText('Enter the transfer steps in English. Use the placeholder :price to display the dynamic price (e.g. "Transfer exactly :price EGP").')
                            ->required(),
                    ])
                    ->columns(1),
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
