<?php

namespace App\Filament\Widgets;

use App\Models\Penjualan;
use App\Models\ProdukPenjualan;
use App\Models\Pengeluaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ProfitSummaryWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Hitung total pendapatan dari penjualan produk bulan ini
        $totalPendapatanBulanIni = ProdukPenjualan::query()
            ->join('penjualans', 'produk_penjualans.penjualan_id', '=', 'penjualans.id')
            ->whereMonth('penjualans.tanggal', now()->month)
            ->whereYear('penjualans.tanggal', now()->year)
            ->sum(DB::raw('produk_penjualans.quantity * produk_penjualans.unit_price'));

        // Hitung total pendapatan dari service HP bulan ini
        $totalPendapatanServiceBulanIni = DB::table('service_hps')
            ->whereMonth('tanggal_selesai', now()->month)
            ->whereYear('tanggal_selesai', now()->year)
            ->whereIn('status', ['Selesai', 'Diambil'])
            ->sum('biaya_service');

        // Hitung total pendapatan dari langganan WiFi bulan ini
        $totalPendapatanWifiBulanIni = DB::table('langganan_wifis')
            ->where('status', 'aktif')
            ->sum('biaya_bulanan');

        // Hitung total pengeluaran bulan ini
        $totalPengeluaranBulanIni = Pengeluaran::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah');

        // Hitung total keuntungan bersih bulan ini
        $totalKeuntunganBersih = ($totalPendapatanBulanIni + $totalPendapatanServiceBulanIni + $totalPendapatanWifiBulanIni) - $totalPengeluaranBulanIni;

        // Hitung total keuntungan dari penjualan produk saja (harga jual - harga beli)
        $totalKeuntunganProduk = ProdukPenjualan::query()
            ->join('penjualans', 'produk_penjualans.penjualan_id', '=', 'penjualans.id')
            ->join('produks', 'produk_penjualans.produk_id', '=', 'produks.id')
            ->whereMonth('penjualans.tanggal', now()->month)
            ->whereYear('penjualans.tanggal', now()->year)
            ->sum(DB::raw('produk_penjualans.quantity * (produks.harga_jual_toko - produks.harga_beli)'));

        // Hitung persentase keuntungan dari total pendapatan
        $persentaseKeuntungan = 0;
        $totalPendapatan = $totalPendapatanBulanIni + $totalPendapatanServiceBulanIni + $totalPendapatanWifiBulanIni;
        if ($totalPendapatan > 0) {
            $persentaseKeuntungan = ($totalKeuntunganBersih / $totalPendapatan) * 100;
        }

        return [
            Stat::make('Total Pendapatan Bulan Ini', 'Rp ' . number_format($totalPendapatan, 0, ',', '.'))
                ->description('Dari penjualan, service & WiFi')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Total Pengeluaran Bulan Ini', 'Rp ' . number_format($totalPengeluaranBulanIni, 0, ',', '.'))
                ->description('Semua pengeluaran bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Keuntungan Bersih', 'Rp ' . number_format($totalKeuntunganBersih, 0, ',', '.'))
                ->description(number_format($persentaseKeuntungan, 1) . '% dari total pendapatan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Keuntungan Produk', 'Rp ' . number_format($totalKeuntunganProduk, 0, ',', '.'))
                ->description('Dari penjualan produk saja')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),
        ];
    }
}
