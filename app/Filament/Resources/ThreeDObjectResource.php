<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ThreeDObjectResource\Pages;
use App\Models\ThreeDObject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ThreeDObjectResource extends Resource
{

    protected static ?string $model = ThreeDObject::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = '3D Objects';
    protected static ?string $modelLabel = '3D Object';
    protected static ?string $pluralModelLabel = '3D Objects';
    protected static ?string $navigationGroup = 'Store Management';

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
                            ->unique(ThreeDObject::class, 'slug', ignoreRecord: true)
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Media & Files')
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail')
                            ->image()
                            ->directory('objects/thumbnails')
                            ->visibility('public'),
                        Forms\Components\FileUpload::make('file_path')
                            ->directory('objects/files')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->label('3D Object File (.ZIP, .RAR, .PDF, etc.)')
                            ->required(fn (string $operation) => $operation === 'create'),
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
                        Forms\Components\TextInput::make('purchase_url')
                            ->url()
                            ->placeholder('https://...'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Descriptions')
                    ->schema([
                        Forms\Components\TextInput::make('description_header')
                            ->placeholder('Brief summary description header')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
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
                Tables\Columns\TextColumn::make('price')
                    ->money('EGP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_price')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(),
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
            'index' => Pages\ListThreeDObjects::route('/'),
            'create' => Pages\CreateThreeDObject::route('/create'),
            'edit' => Pages\EditThreeDObject::route('/{record}/edit'),
        ];
    }
}
