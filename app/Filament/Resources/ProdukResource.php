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
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->required()
                    ->placeholder('Unggah gambar produk'),
                Forms\Components\TextInput::make('nama_produk')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('Masukkan nama produk'),
                Forms\Components\TextInput::make('qty_stok')
                    ->label('Jumlah Stok')
                    ->required()
                    ->numeric()
                    ->placeholder('Masukkan jumlah stok'),
                Forms\Components\TextInput::make('harga_beli')
                    ->label('Harga Beli (Rp)')
                    ->required()
                    ->numeric()
                    ->placeholder('Masukkan harga beli produk'),
                Forms\Components\TextInput::make('harga_jual_toko')
                    ->label('Harga Jual di Toko (Rp)')
                    ->required()
                    ->numeric()
                    ->placeholder('Masukkan harga jual produk'),
                Forms\Components\Select::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama')
                    ->required()
                    ->placeholder('Pilih kategori produk'),


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
                Tables\Columns\TextColumn::make('kategori.nama')
                    ->label('Kategori')
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
