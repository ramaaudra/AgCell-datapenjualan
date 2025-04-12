<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Filament\Resources\ProdukResource\RelationManagers;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static ?string $navigationIcon = 'heroicon-m-building-storefront';

    //navigation label
    protected static ?string $navigationLabel = 'Produk';

    //navigation group
    protected static ?string $navigationGroup = 'Produk';

    //navigation sort
    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk')
                    ->description('Detail informasi produk')
                    ->icon('heroicon-m-building-storefront')
                    ->columns(2)
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Gambar Produk')
                            ->image()
                            ->required()
                            ->placeholder('Unggah gambar produk')
                            ->helperText('Format: JPG, PNG. Ukuran max: 2MB')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('300')
                            ->imageResizeTargetHeight('300'),
                        Forms\Components\TextInput::make('nama_produk')
                            ->label('Nama Produk')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Contoh: Headset Gaming XYZ')
                            ->helperText('Nama lengkap produk untuk ditampilkan'),
                        Forms\Components\Select::make('kategori_id')
                            ->label('Kategori')
                            ->relationship('kategori', 'nama')
                            ->required()
                            ->searchable()
                            ->placeholder('Pilih kategori produk')
                            ->helperText('Kategori untuk pengelompokan produk'),
                    ]),

                Forms\Components\Section::make('Informasi Stok & Harga')
                    ->description('Detail stok dan harga produk')
                    ->icon('heroicon-m-currency-dollar')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('qty_stok')
                            ->label('Jumlah Stok')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('Contoh: 10')
                            ->helperText('Jumlah stok tersedia'),
                        Forms\Components\TextInput::make('harga_beli')
                            ->label('Harga Beli (Rp)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('Contoh: 50000')
                            ->helperText('Harga modal pembelian produk'),
                        Forms\Components\TextInput::make('harga_jual_toko')
                            ->label('Harga Jual (Rp)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('Contoh: 75000')
                            ->helperText('Harga jual ke pelanggan'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->square(),
                Tables\Columns\TextColumn::make('nama_produk')
                    ->label('Nama Produk')
                    ->description(fn($record): string => $record->kategori->nama ?? '-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('qty_stok')
                    ->label('Jumlah Stok')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_beli')
                    ->label('Harga Beli (Rp)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_jual_toko')
                    ->label('Harga Jual di Toko (Rp)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
}
