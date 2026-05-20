<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Models\Enrollment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Enrollments';
    protected static ?string $modelLabel = 'Enrollment';
    protected static ?string $pluralModelLabel = 'Enrollments';
    protected static ?string $navigationGroup = 'Store Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Enrollment Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->placeholder('Select a student')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('type')
                            ->label('Product Type')
                            ->options([
                                'course' => 'Course',
                                'addon' => 'Addon',
                                'object' => '3D Object',
                            ])
                            ->required()
                            ->live()
                            ->dehydrated(false) // Not persisted in DB directamente
                            ->afterStateHydrated(function ($state, $record, $set) {
                                if ($record) {
                                    if ($record->course_id) $set('type', 'course');
                                    elseif ($record->addon_id) $set('type', 'addon');
                                    elseif ($record->three_d_object_id) $set('type', 'object');
                                }
                            })
                            ->afterStateUpdated(function ($state, $set) {
                                $set('course_id', null);
                                $set('addon_id', null);
                                $set('three_d_object_id', null);
                                $set('price_paid', 0.00);
                            }),

                        Forms\Components\Select::make('course_id')
                            ->label('Course')
                            ->relationship('course', 'title')
                            ->preload()
                            ->searchable()
                            ->placeholder('Select a course')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'course')
                            ->required(fn (Forms\Get $get) => $get('type') === 'course')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $course = \App\Models\Course::find($state);
                                    if ($course) {
                                        $set('price_paid', $course->discount_price ?? $course->price);
                                    }
                                }
                            }),

                        Forms\Components\Select::make('addon_id')
                            ->label('Addon')
                            ->relationship('addon', 'title')
                            ->preload()
                            ->searchable()
                            ->placeholder('Select an addon')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'addon')
                            ->required(fn (Forms\Get $get) => $get('type') === 'addon')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $addon = \App\Models\Addon::find($state);
                                    if ($addon) {
                                        $set('price_paid', $addon->discount_price ?? $addon->price);
                                    }
                                }
                            }),

                        Forms\Components\Select::make('three_d_object_id')
                            ->label('3D Object')
                            ->relationship('threeDObject', 'title')
                            ->preload()
                            ->searchable()
                            ->placeholder('Select a 3D object')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'object')
                            ->required(fn (Forms\Get $get) => $get('type') === 'object')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $object = \App\Models\ThreeDObject::find($state);
                                    if ($object) {
                                        $set('price_paid', $object->discount_price ?? $object->price);
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('price_paid')
                            ->required()
                            ->numeric()
                            ->prefix('EGP')
                            ->placeholder('0.00'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'active' => 'Active',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Product Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Course' => 'info',
                        'Addon' => 'warning',
                        '3D Object' => 'success',
                        default => 'gray',
                    })
                    ->state(function (Enrollment $record): string {
                        if ($record->course_id) return 'Course';
                        if ($record->addon_id) return 'Addon';
                        if ($record->three_d_object_id) return '3D Object';
                        return 'Unknown';
                    }),

                Tables\Columns\TextColumn::make('product_title')
                    ->label('Product')
                    ->state(function (Enrollment $record): string {
                        if ($record->course) return $record->course->title;
                        if ($record->addon) return $record->addon->title;
                        if ($record->threeDObject) return $record->threeDObject->title;
                        return '-';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('course', function (Builder $q) use ($search) {
                            $q->where('title', 'like', "%{$search}%");
                        })->orWhereHas('addon', function (Builder $q) use ($search) {
                            $q->where('title', 'like', "%{$search}%");
                        })->orWhereHas('threeDObject', function (Builder $q) use ($search) {
                            $q->where('title', 'like', "%{$search}%");
                        });
                    }),

                Tables\Columns\TextColumn::make('price_paid')
                    ->money('EGP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Product Type')
                    ->options([
                        'course' => 'Course',
                        'addon' => 'Addon',
                        'object' => '3D Object',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['value'] === 'course', fn ($q) => $q->whereNotNull('course_id'))
                            ->when($data['value'] === 'addon', fn ($q) => $q->whereNotNull('addon_id'))
                            ->when($data['value'] === 'object', fn ($q) => $q->whereNotNull('three_d_object_id'));
                    }),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Student')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('From: ' . Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removable(fn () => $data['created_from'] = null);
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('To: ' . Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removable(fn () => $data['created_until'] = null);
                        }
                        return $indicators;
                    }),
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
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
