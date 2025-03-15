<?php

namespace App\Filament\Widgets;

use App\Models\LanggananWifi;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LanggananWifiTerdekatWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Langganan WiFi';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LanggananWifi::query()
                    ->where('status', 'aktif')
                    ->orderBy('tanggal_berakhir', 'asc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_telepon')
                    ->label('No. Telepon'),
                Tables\Columns\TextColumn::make('tanggal_berakhir')
                    ->label('Tanggal Berakhir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sisa_hari')
                    ->badge()
                    ->label('Sisa Waktu')
                    ->getStateUsing(function ($record) {
                        $today = now();
                        $end = \Carbon\Carbon::parse($record->tanggal_berakhir);
                        $days = intval($today->diffInDays($end, false));
                        return $days < 0 ? 'Sudah Berakhir' : $days . ' Hari';
                    })
                    ->color(function ($record) {
                        $today = now();
                        $end = \Carbon\Carbon::parse($record->tanggal_berakhir);
                        $days = intval($today->diffInDays($end, false));
                        return match (true) {
                            $days < 0 => 'danger',
                            $days <= 7 => 'warning',
                            $days <= 30 => 'info',
                            default => 'success',
                        };
                    }),
                Tables\Columns\TextColumn::make('biaya_bulanan')
                    ->label('Biaya Bulanan')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('detail')
                    ->label('Detail')
                    ->url(fn(LanggananWifi $record): string => route('filament.admin.resources.langganan-wifis.edit', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
