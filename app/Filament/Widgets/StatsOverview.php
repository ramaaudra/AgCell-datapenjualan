<?php

namespace App\Filament\Widgets;

use App\Models\Pengeluaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Penjualan;

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
        return [

            Stat::make('Total Omzet Total (Rp)', number_format($totalPenjualan)),
            Stat::make('Total Omzet Hari Ini (Rp)',number_format($totalPenjualanHariIni)),
            Stat::make('Total Omzet Bulan ini (Rp)', number_format($totalPenjualanBulanIni)),
            Stat::make('Total Pengeluaran Bulan ini (Rp)', number_format($totalPengeluaranBulanIni)),
        ];
    }
}
