<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class ManageGeneralSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string $view = 'filament.pages.manage-general-settings';

    protected static ?string $navigationLabel = 'General Settings';

    protected static ?string $title = 'General Settings';

    protected static ?string $navigationGroup = 'Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'site_name_ar' => Setting::get('site_name_ar', 'أنيمفاي'),
            'site_name_en' => Setting::get('site_name_en', 'Animfy'),
            'site_logo' => Setting::get('site_logo'),
            'contact_phone' => Setting::get('contact_phone', '01012345678'),
            'contact_email' => Setting::get('contact_email', 'info@animfy.com'),
            'social_facebook' => Setting::get('social_facebook', 'https://www.facebook.com/animfy.studio'),
            'social_instagram' => Setting::get('social_instagram', 'https://www.instagram.com/animfy.studio/'),
            'social_youtube' => Setting::get('social_youtube', 'https://www.youtube.com/@animfy.studio'),
            'social_tiktok' => Setting::get('social_tiktok', 'https://www.tiktok.com/@animfy.studio'),
            'meta_title_ar' => Setting::get('meta_title_ar', 'أكاديمية أنيمفاي - تعليم ثلاثي الأبعاد والأنيميشن'),
            'meta_title_en' => Setting::get('meta_title_en', 'Animfy Academy - Learn 3D & Animation'),
            'meta_description_ar' => Setting::get('meta_description_ar', 'منصة احترافية لتعلم الرسوم المتحركة ثلاثية الأبعاد، المونتاج، والذكاء الاصطناعي من الصفر وحتى الاحتراف.'),
            'meta_description_en' => Setting::get('meta_description_en', 'Animfy is a creative studio teaching 3D, video editing, and AI tools from scratch to professional.'),
            'meta_keywords' => Setting::get('meta_keywords', 'blender, 3d, animation, vfx, video editing, courses'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Website Identity')
                    ->description('Set your website logo and names for Arabic and English locales.')
                    ->schema([
                        TextInput::make('site_name_ar')
                            ->label('Website Name (Arabic)')
                            ->required(),
                        TextInput::make('site_name_en')
                            ->label('Website Name (English)')
                            ->required(),
                        FileUpload::make('site_logo')
                            ->label('Website Logo')
                            ->image()
                            ->directory('settings')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Contact & Social Media')
                    ->description('Specify phone numbers, email, and social networks handles.')
                    ->schema([
                        TextInput::make('contact_phone')
                            ->label('Phone Number')
                            ->tel(),
                        TextInput::make('contact_email')
                            ->label('Contact Email')
                            ->email(),
                        TextInput::make('social_facebook')
                            ->label('Facebook Page URL')
                            ->url(),
                        TextInput::make('social_instagram')
                            ->label('Instagram URL')
                            ->url(),
                        TextInput::make('social_youtube')
                            ->label('YouTube Channel URL')
                            ->url(),
                        TextInput::make('social_tiktok')
                            ->label('TikTok URL')
                            ->url(),
                    ])
                    ->columns(2),

                Section::make('SEO & Meta Configurations')
                    ->description('Optimize your search engine presence with title tags and descriptions.')
                    ->schema([
                        TextInput::make('meta_title_ar')
                            ->label('Meta Title (Arabic)')
                            ->required(),
                        TextInput::make('meta_title_en')
                            ->label('Meta Title (English)')
                            ->required(),
                        Textarea::make('meta_description_ar')
                            ->label('Meta Description (Arabic)')
                            ->rows(3)
                            ->required(),
                        Textarea::make('meta_description_en')
                            ->label('Meta Description (English)')
                            ->rows(3)
                            ->required(),
                        TextInput::make('meta_keywords')
                            ->label('Meta Keywords (Comma separated)')
                            ->placeholder('e.g. blender, 3d, animation'),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save General Settings')
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
                ->title('General settings saved successfully!')
                ->send();
        } catch (\Exception $exception) {
            Notification::make()
                ->danger()
                ->title('Error saving general settings')
                ->body($exception->getMessage())
                ->send();
        }
    }
}
