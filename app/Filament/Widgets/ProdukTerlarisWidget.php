<?php

namespace App\Filament\Widgets;

use App\Models\ProdukPenjualan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class ProdukTerlarisWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Produk Terlaris Minggu Ini';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProdukPenjualan::query()
                    ->select(
                        'produk_id',
                        DB::raw('SUM(quantity) as total_quantity'),
                        DB::raw('SUM(quantity * unit_price) as total_revenue')
                    )
                    ->whereHas('penjualan', function ($query) {
                        $query->whereBetween('tanggal', [
                            now()->startOfWeek()->format('Y-m-d'),
                            now()->endOfWeek()->format('Y-m-d')
                        ]);
                    })
                    ->whereNotNull('produk_id')
                    ->groupBy('produk_id')
                    ->orderByDesc('total_quantity')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('produk.nama_produk')
                    ->label('Nama Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('produk.kategori.nama')
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Jumlah Terjual')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Total Pendapatan')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('detail')
                    ->label('Detail Produk')
                    ->url(fn($record): string => route('filament.admin.resources.produks.edit', ['record' => $record->produk_id]))
                    ->icon('heroicon-o-eye'),
            ])
            ->emptyStateHeading('Belum ada penjualan minggu ini')
            ->emptyStateDescription('Data penjualan produk akan muncul di sini')
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->striped();
    }
}
