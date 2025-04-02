<?php

namespace App\Filament\Widgets;

use App\Models\ServiceHp;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ServiceHpTableWidget extends BaseWidget
{
    protected static ?int $sort = 99;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ServiceHp::query()
                    ->whereIn('status', ['Selesai', 'Diambil'])
                    ->latest('tanggal_selesai')
            )
            ->heading('Service HP Selesai & Diambil')
            ->description('Daftar service HP yang sudah selesai dan diambil')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('merk_hp')
                    ->label('Merk HP'),
                Tables\Columns\TextColumn::make('model_hp')
                    ->label('Model HP'),
                Tables\Columns\TextColumn::make('jenis_kerusakan')
                    ->label('Jenis Kerusakan'),
                Tables\Columns\TextColumn::make('biaya_service')
                    ->label('Biaya Service')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Selesai' => 'success',
                        'Diambil' => 'info',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('tanggal_selesai', 'desc')
            ->striped();
    }
}
