<?php

namespace App\Filament\Widgets;

use App\Models\Produk;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockProductsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Produk dengan Stok Menipis';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Produk::query()
                    ->where('qty_stok', '<=', 5)
                    ->orderBy('qty_stok', 'asc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_produk')
                    ->label('Nama Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori.nama')
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('qty_stok')
                    ->label('Stok Tersisa')
                    ->sortable()
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= 3 => 'warning',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('harga_beli')
                    ->label('Harga Beli')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_jual_toko')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('detail')
                    ->label('Detail Produk')
                    ->url(fn(Produk $record): string => route('filament.admin.resources.produks.edit', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ])
            ->emptyStateHeading('Tidak ada produk dengan stok menipis')
            ->emptyStateDescription('Semua produk memiliki stok yang cukup')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->striped();
    }
}
