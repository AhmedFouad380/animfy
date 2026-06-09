<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationLabel = 'Payments';
    protected static ?string $modelLabel = 'Payment';
    protected static ?string $pluralModelLabel = 'Payments';
    protected static ?string $navigationGroup = 'Store Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Transaction Details')
                    ->schema([
                        Forms\Components\Select::make('enrollment_id')
                            ->relationship('enrollment', 'id')
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $productName = '';
                                if ($record->course) {
                                    $productName = "Course: {$record->course->title}";
                                } elseif ($record->addon) {
                                    $productName = "Addon: {$record->addon->title}";
                                } elseif ($record->threeDObject) {
                                    $productName = "3D Object: {$record->threeDObject->title}";
                                }
                                $studentName = $record->user ? $record->user->name : 'Unknown';
                                return "Student: {$studentName} - {$productName}";
                            })
                            ->required()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('transaction_reference')
                            ->required()
                            ->maxLength(255)
                            ->label('Paymob Transaction ID'),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('EGP')
                            ->placeholder('0.00'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'success' => 'Success',
                                'failed' => 'Failed',
                                'pending' => 'Pending',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('payment_method')
                            ->required()
                            ->placeholder('e.g., card, wallet, kiosk')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Gateway Logs')
                    ->schema([
                        Forms\Components\KeyValue::make('paymob_payload')
                            ->label('Raw Callback Payload')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_reference')
                    ->label('Transaction ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('enrollment.user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_title')
                    ->label('Product')
                    ->state(function (Payment $record): string {
                        $enrollment = $record->enrollment;
                        if (!$enrollment) return '-';
                        if ($enrollment->course) return 'Course: ' . $enrollment->course->title;
                        if ($enrollment->addon) return 'Addon: ' . $enrollment->addon->title;
                        if ($enrollment->threeDObject) return '3D Object: ' . $enrollment->threeDObject->title;
                        return '-';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('enrollment', function ($q) use ($search) {
                            $q->whereHas('course', fn($c) => $c->where('title', 'like', "%{$search}%"))
                              ->orWhereHas('addon', fn($a) => $a->where('title', 'like', "%{$search}%"))
                              ->orWhereHas('threeDObject', fn($o) => $o->where('title', 'like', "%{$search}%"));
                        });
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('EGP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'pending' => 'Pending',
                    ])
                    ->label('Transaction Status'),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From Date')
                            ->placeholder('Select start date'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('To Date')
                            ->placeholder('Select end date'),
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
                            $indicators[] = Tables\Filters\Indicator::make('From: ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removable(fn () => $data['created_from'] = null);
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('To: ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString())
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
