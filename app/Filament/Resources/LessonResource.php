<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LessonResource\Pages;
use App\Models\Lesson;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LessonResource extends Resource
{

    protected static ?string $model = Lesson::class;

    protected static ?string $navigationIcon = 'heroicon-o-play-circle';

    protected static ?string $navigationLabel = 'Lessons';
    protected static ?string $modelLabel = 'Lesson';
    protected static ?string $pluralModelLabel = 'Lessons';
    protected static ?string $navigationGroup = 'Education Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Lesson Details')
                    ->schema([
                        Forms\Components\Select::make('chapter_id')
                            ->relationship('chapter', 'title')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->columnSpan(1),
                    ])->columns(5),

                Forms\Components\Section::make('Content & Media')
                    ->schema([
                        Forms\Components\FileUpload::make('video_path')
                            ->label('Video File (Supports files up to 3GB)')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->directory('lessons/videos')
                            ->visibility('public')
                            ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/x-matroska', 'video/x-msvideo'])
                            ->maxSize(3 * 1024 * 1024) // Max size: 3GB (in KB: 3,145,728)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('attachment_path')
                            ->directory('lessons/attachments')
                            ->visibility('public')
                            ->placeholder('Upload course materials (PDF, ZIP, etc.)'),
                        Forms\Components\TextInput::make('duration_minutes')
                            ->label('Duration (HH:MM:SS)')
                            ->placeholder('00:00:00')
                            ->required()
                            ->formatStateUsing(function ($state) {
                                if (!$state) return '00:00:00';
                                $totalSeconds = (int) round($state * 60);
                                $hours = floor($totalSeconds / 3600);
                                $minutes = floor(($totalSeconds / 60) % 60);
                                $seconds = $totalSeconds % 60;
                                return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                            })
                            ->dehydrateStateUsing(function ($state) {
                                if (!$state) return 0;
                                $parts = explode(':', $state);
                                if (count($parts) === 3) {
                                    $hours = (int) $parts[0];
                                    $minutes = (int) $parts[1];
                                    $seconds = (int) $parts[2];
                                    return ($hours * 60) + $minutes + ($seconds / 60);
                                } elseif (count($parts) === 2) {
                                    $minutes = (int) $parts[0];
                                    $seconds = (int) $parts[1];
                                    return $minutes + ($seconds / 60);
                                }
                                return (float) $state;
                            }),
                        Forms\Components\Toggle::make('is_preview')
                            ->label('Free Preview Lesson')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('chapter.title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('chapter.course.title')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->state(function (Lesson $record) {
                        $state = $record->duration_minutes;
                        if (!$state) return '00:00:00';
                        $totalSeconds = (int) round($state * 60);
                        $hours = floor($totalSeconds / 3600);
                        $minutes = floor(($totalSeconds / 60) % 60);
                        $seconds = $totalSeconds % 60;
                        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_preview')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_preview'),
                Tables\Filters\SelectFilter::make('course')
                    ->relationship('chapter.course', 'title')
                    ->preload()
                    ->searchable()
                    ->label('Course'),
                Tables\Filters\SelectFilter::make('chapter_id')
                    ->relationship('chapter', 'title')
                    ->preload()
                    ->searchable(),
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
            'index' => Pages\ListLessons::route('/'),
            'create' => Pages\CreateLesson::route('/create'),
            'edit' => Pages\EditLesson::route('/{record}/edit'),
        ];
    }
}
