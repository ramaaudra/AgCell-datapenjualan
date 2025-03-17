<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenjualanResource\Pages;
use App\Filament\Resources\PenjualanResource\RelationManagers;
use App\Models\Penjualan;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use App\Models\ProdukPenjualan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;
    protected static ?string $navigationIcon = 'heroicon-m-shopping-cart';
    protected static ?string $pluralModelLabel = 'Penjualan';
    protected static ?string $modelLabel = 'Penjualan';
    protected static ?string $navigationLabel = 'Penjualan';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal Orderan')
                    ->required(),
                Forms\Components\TextInput::make('pembeli')
                    ->required()
                    ->placeholder('Masukkan nama pembeli')
                    ->maxLength(200),
                Forms\Components\Section::make('Produk dipesan')->schema([
                    self::getItemsRepeater(),
                ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('jumlah')
                                    ->label('Jumlah (Rp)')
                                    ->placeholder('Total jumlah')
                                    ->required()
                                    ->readOnly()
                                    ->numeric(),
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal Orderan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah (Rp)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('produk_terjual')
                    ->label('Produk Terjual')
                    ->getStateUsing(function (Penjualan $record) {
                        $products = $record->orderProducts()->with('produk')->get()
                            ->map(function ($item) {
                                return $item->produk->nama_produk . ' (' . $item->quantity . ')';
                            })->join(', ');

                        return $products ?: '-';
                    }),
                Tables\Columns\TextColumn::make('pembeli')
                    ->sortable(),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('orderProducts')
            ->relationship()
            ->live()
            ->columns([
                'md' => 10,
            ])
            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                self::updateTotalPrice($get, $set);
            })
            ->schema([
                Forms\Components\Select::make('produk_id')
                    ->label('Produk')
                    ->required()
                    ->options(Produk::query()->where('qty_stok', '>', 1)->pluck('nama_produk', 'id'))
                    ->columnSpan([
                        'md' => 5
                    ])
                    ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get, $state) {
                        $product = Produk::find($state);
                        $set('unit_price', $product->harga_jual_toko ?? 0);
                        $set('stok', $product->qty_stok ?? 0);
                    })
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $product = Produk::find($state);
                        $set('unit_price', $product->harga_jual_toko ?? 0);
                        $set('stok', $product->qty_stok ?? 0);
                        $quantity = $get('quantity') ?? 1;
                        $stock = $get('qty_stok') ?? 0;
                        self::updateTotalPrice($get, $set);
                    })
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->columnSpan([
                        'md' => 1
                    ])
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $stock = $get('stok');
                        if ($state > $stock) {
                            $set('quantity', $stock);
                            Notification::make()
                                ->title('Stok tidak mencukupi')
                                ->warning()
                                ->send();
                        }
                        self::updateTotalPrice($get, $set);
                    }),
                Forms\Components\TextInput::make('stok')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->columnSpan([
                        'md' => 1
                    ]),
                Forms\Components\TextInput::make('unit_price')
                    ->label('Harga saat ini')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->columnSpan([
                        'md' => 3
                    ]),
            ]);
    }

    protected static function updateTotalPrice(Forms\Get $get, Forms\Set $set): void
    {
        $selectedProducts = collect($get('orderProducts'))->filter(
            fn($item) =>
            !empty($item['produk_id']) && !empty($item['quantity'])
        );
        $prices = Produk::find($selectedProducts->pluck('produk_id'))->pluck('harga_jual_toko', 'id');
        $total = $selectedProducts->reduce(function ($total, $product) use ($prices) {
            return $total + ($prices[$product['produk_id']] * $product['quantity']);
        }, 0);

        $set('jumlah', $total);
    }
}
