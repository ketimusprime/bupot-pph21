<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxCutResource\Pages;
use App\Models\TaxCut;
use App\Models\Supplier;
use App\Models\PartTimer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Services\TaxCutCalculator;

class TaxCutResource extends Resource
{
    protected static ?string $model = TaxCut::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Bukti Potong PPh 21';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 1;

    /* ===============================
     * Helpers (BEST PRACTICE)
     * =============================== */

    protected static function rupiah(): RawJs
    {
        return RawJs::make('$money($input)');
    }

    protected static function recalculate(Get $get, Set $set): void
    {
        $result = TaxCutCalculator::calculate(
            (float) $get('commission_amount'),
            (float) $get('tax_rate'),
            $get('pph_method') ?? 'gross'
        );
    
        $set('dpp_amount', $result['dpp_amount']);
        $set('tax_amount', $result['tax_amount']);
        $set('net_payment', $result['net_payment']);
    }

    /* ===============================
     * FORM
     * =============================== */

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Perusahaan')
                ->schema([
                    Forms\Components\Select::make('company_id')
                        ->label('PT / CV')
                        ->relationship('company', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                ]),

            Forms\Components\Section::make('Penerima')
                ->schema([
                    Forms\Components\Select::make('recipient_type')
                        ->label('Tipe Penerima')
                        ->options([
                            Supplier::class  => 'Supplier',
                            PartTimer::class => 'Part Timer / Freelancer',
                        ])
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('recipient_id', null)),

                    Forms\Components\Select::make('recipient_id')
                        ->label('Nama Penerima')
                        ->options(fn (Get $get) => match ($get('recipient_type')) {
                            Supplier::class  => Supplier::where('is_active', true)->pluck('name', 'id'),
                            PartTimer::class => PartTimer::where('is_active', true)->pluck('name', 'id'),
                            default          => [],
                        })
                        ->required()
                        ->searchable()
                        ->preload()
                        ->disabled(fn (Get $get) => blank($get('recipient_type'))),
                ])
                ->columns(2),

            Forms\Components\Section::make('Detail Pembayaran')
                ->schema([
                    Forms\Components\TextInput::make('memo_number')
                        ->label('No. Memo Intern')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('invoice_number')
                        ->label('No. Invoice')
                        ->required(),

                    Forms\Components\DatePicker::make('invoice_date')
                        ->label('Tanggal Invoice')
                        ->required()
                        ->native(false),

                    Forms\Components\DatePicker::make('cut_date')
                        ->label('Tanggal Dipotong')
                        ->default(now())
                        ->required(),

                    Forms\Components\TextInput::make('commission_amount')
                        ->label('Bruto Komisi')
                        ->numeric()
                        ->mask(static::rupiah())
                        ->stripCharacters(',')
                        ->prefix('Rp')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Get $get, Set $set) =>
                            static::recalculate($get, $set)
                        ),

                    Forms\Components\TextInput::make('dpp_amount')
                        ->label('DPP (50%)')
                        ->numeric()
                        ->mask(static::rupiah())
                        ->stripCharacters(',')
                        ->prefix('Rp')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\TextInput::make('tax_rate')
                        ->label('Tarif Pajak')
                        ->numeric()
                        ->suffix('%')
                        ->default(5)
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Get $get, Set $set) =>
                            static::recalculate($get, $set)
                        ),

                    Forms\Components\Select::make('pph_method')
                        ->label('Metode PPh 21')
                        ->options([
                            'gross'    => 'Gross (Dipotong)',
                            'gross_up' => 'Gross Up (Ditanggung Perusahaan)',
                        ])
                        ->required()
                        ->live()
                        ->afterStateHydrated(function (Set $set, $state) {
                            // HANYA CREATE
                            if ($state === null) {
                                $set('pph_method', 'gross');
                            }
                        })
                        ->afterStateUpdated(fn (Get $get, Set $set) =>
                            static::recalculate($get, $set)
                        ),

                    Forms\Components\TextInput::make('tax_amount')
                        ->label('PPh 21')
                        ->numeric()
                        ->mask(static::rupiah())
                        ->stripCharacters(',')
                        ->prefix('Rp')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\TextInput::make('net_payment')
                        ->label('Total Bayar')
                        ->numeric()
                        ->mask(static::rupiah())
                        ->stripCharacters(',')
                        ->prefix('Rp')
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\Textarea::make('notes')
                        ->label('Catatan')
                        ->columnSpanFull(),
                ])
                ->columns(3),
        ]);
    }

    /* ===============================
     * TABLE (FITUR LAMA TETAP ADA)
     * =============================== */

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('memo_number')
                    ->label('No. Memo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('company.name')
                    ->label('Perusahaan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('recipient.name')
                    ->label('Penerima')
                    ->searchable(),

                Tables\Columns\TextColumn::make('recipient_type')
                    ->label('Tipe')
                    ->formatStateUsing(fn ($state) =>
                        $state === Supplier::class ? 'Supplier' : 'Part Timer'
                    )
                    ->badge()
                    ->color(fn ($state) =>
                        $state === Supplier::class ? 'info' : 'warning'
                    ),

                Tables\Columns\TextColumn::make('commission_amount')
                    ->label('Komisi')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tax_amount')
                    ->label('PPh 21')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('net_payment')
                    ->label('Total Bayar')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('cut_date')
                    ->label('Tgl Potong')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Perusahaan')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('recipient_type')
                    ->label('Tipe Penerima')
                    ->options([
                        Supplier::class  => 'Supplier',
                        PartTimer::class => 'Part Timer',
                    ]),

                Tables\Filters\Filter::make('cut_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari'),
                        Forms\Components\DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(fn ($query, array $data) =>
                        $query
                            ->when($data['from'], fn ($q) => $q->whereDate('cut_date', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('cut_date', '<=', $data['until']))
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn (TaxCut $record) => route('tax-cut.pdf', $record))
                    ->openUrlInNewTab(),

                    Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->can('taxcut.edit')),
            
                    Tables\Actions\DeleteAction::make()
                        ->visible(fn () => auth()->user()->can('taxcut.delete')),
            ])
            ->headerActions([
                Tables\Actions\Action::make('import')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->url(fn () => TaxCutResource::getUrl('import')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTaxCuts::route('/'),
            'create' => Pages\CreateTaxCut::route('/create'),
            'edit'   => Pages\EditTaxCut::route('/{record}/edit'),
            'import' => Pages\ImportTaxCut::route('/import'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('taxcut.view');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('taxcut.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('taxcut.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('taxcut.delete');
    }

}
