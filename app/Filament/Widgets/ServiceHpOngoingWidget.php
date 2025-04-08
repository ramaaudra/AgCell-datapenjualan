<?php

namespace App\Filament\Widgets;

use App\Models\ServiceHp;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ServiceHpOngoingWidget extends BaseWidget
{
    protected static ?int $sort = 98;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Service HP Dalam Proses';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ServiceHp::query()
                    ->whereIn('status', ['Proses', 'Menunggu Sparepart'])
                    ->latest('tanggal_masuk')
                    ->limit(5)
            )
            ->heading('Service HP Dalam Proses')
            ->description('Daftar service HP yang sedang dikerjakan')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_telepon')
                    ->label('No. Telepon'),
                Tables\Columns\TextColumn::make('merk_hp')
                    ->label('Merk HP'),
                Tables\Columns\TextColumn::make('model_hp')
                    ->label('Model HP'),
                Tables\Columns\TextColumn::make('jenis_kerusakan')
                    ->label('Jenis Kerusakan'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Proses' => 'warning',
                        'Menunggu Sparepart' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('durasi')
                    ->label('Durasi')
                    ->getStateUsing(function ($record) {
                        $start = \Carbon\Carbon::parse($record->tanggal_masuk);
                        $now = now();
                        $days = intval($start->diffInDays($now));
                        return $days . ' Hari';
                    })
                    ->badge()
                    ->color(function ($record) {
                        $start = \Carbon\Carbon::parse($record->tanggal_masuk);
                        $now = now();
                        $days = intval($start->diffInDays($now));
                        return match (true) {
                            $days > 14 => 'danger',
                            $days > 7 => 'warning',
                            default => 'info',
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('detail')
                    ->label('Detail')
                    ->url(fn(ServiceHp $record): string => route('filament.admin.resources.service-hps.edit', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ])
            ->emptyStateHeading('Tidak ada service HP dalam proses')
            ->emptyStateDescription('Semua service HP sudah selesai')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->striped();
    }
}
