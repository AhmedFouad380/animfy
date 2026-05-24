<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourseResource extends Resource
{

    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Courses';
    protected static ?string $modelLabel = 'Course';
    protected static ?string $pluralModelLabel = 'Courses';
    protected static ?string $navigationGroup = 'Education Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(Course::class, 'slug', ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slogan')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Media & Details')
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail')
                            ->image()
                            ->directory('courses/thumbnails')
                            ->visibility('public'),
                        Forms\Components\TextInput::make('video_overview_url')
                            ->url()
                            ->placeholder('https://youtube.com/... or https://vimeo.com/...'),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing & Status')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('EGP')
                            ->placeholder('0.00'),
                        Forms\Components\TextInput::make('discount_price')
                            ->numeric()
                            ->prefix('EGP')
                            ->placeholder('0.00'),
                        Forms\Components\TextInput::make('duration')
                            ->required()
                            ->default('00:00:00')
                            ->placeholder('HH:MM:SS')
                            ->regex('/^\d+:[0-5]\d:[0-5]\d$/')
                            ->validationMessages([
                                'regex' => 'The duration format must be HH:MM:SS (e.g., 02:30:00 or 120:15:30).',
                            ])
                            ->label('Duration (HH:MM:SS)'),
                        Forms\Components\TextInput::make('students_count')
                            ->required()
                            ->numeric()
                            ->default(1500)
                            ->label('Students Count'),
                        Forms\Components\TextInput::make('rating')
                            ->required()
                            ->numeric()
                            ->default(5.0)
                            ->step(0.1)
                            ->minValue(1)
                            ->maxValue(5),
                        Forms\Components\Toggle::make('is_best_seller')
                            ->default(false),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(3),

                Forms\Components\Section::make('Descriptions & Content')
                    ->schema([
                        Forms\Components\TextInput::make('description_header')
                            ->placeholder('Brief summary description header')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
                            ->columnSpanFull(),
                        Forms\Components\TagsInput::make('what_you_will_learn')
                            ->placeholder('Add a feature and press Enter')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price')
                    ->money('EGP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_price')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_best_seller')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('students_count')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('is_best_seller'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
