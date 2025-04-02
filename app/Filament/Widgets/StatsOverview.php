<?php

namespace App\Filament\Widgets;

use App\Models\Pengeluaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Penjualan;
use App\Models\LanggananWifi;
use App\Models\ServiceHp;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    protected function getStats(): array
    {

        //total penjualan hari ini
        $totalPenjualanHariIni = Penjualan::whereDate('tanggal', now())->sum('jumlah');

        //total pelanggan wifi aktif
        $totalPelangganWifi = LanggananWifi::where('status', 'aktif')->count();

        //total service hp
        $totalServiceHp = ServiceHp::count();
        return [

            Stat::make('Total Pendapatan Hari Ini (Rp)', number_format($totalPenjualanHariIni)),
            Stat::make('Total Pelanggan Wifi Aktif', $totalPelangganWifi),
            Stat::make('Total Service HP', $totalServiceHp)
        ];
    }
}
