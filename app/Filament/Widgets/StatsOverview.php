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
    protected function getStats(): array
    {

        $totalPenjualan = Penjualan::sum('jumlah');
        //total penjualan hari ini
        $totalPenjualanHariIni = Penjualan::whereDate('tanggal', now())->sum('jumlah');

        //total penjualan bulan ini
        $totalPenjualanBulanIni = Penjualan::whereMonth('tanggal', now())->sum('jumlah');

        //total pengeluaran bulan ini
        $totalPengeluaranBulanIni = Pengeluaran::whereMonth('tanggal', now())->sum('jumlah');

        //total pelanggan wifi
        $totalPelangganWifi = LanggananWifi::count();

        //total service hp
        $totalServiceHp = ServiceHp::count();
        return [

            Stat::make('Total Omzet Total (Rp)', number_format($totalPenjualan)),
            Stat::make('Total Omzet Hari Ini (Rp)',number_format($totalPenjualanHariIni)),
            Stat::make('Total Omzet Bulan ini (Rp)', number_format($totalPenjualanBulanIni)),
            Stat::make('Total Pengeluaran Bulan ini (Rp)', number_format($totalPengeluaranBulanIni)),
            Stat::make('Total Pelanggan Wifi', $totalPelangganWifi),
            Stat::make('Total Service HP', $totalServiceHp)
        ];
    }
}
