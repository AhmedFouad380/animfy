<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LessonResource\Pages;
use App\Models\Lesson;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;

class LessonResource extends Resource
{
    use Translatable;

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
                            ->required()
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
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->suffix('Minutes'),
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
                    ->numeric()
                    ->suffix(' min')
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
