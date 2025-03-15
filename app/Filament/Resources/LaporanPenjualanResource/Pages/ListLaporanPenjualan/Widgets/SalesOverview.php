<?php

namespace App\Filament\Resources\LaporanPenjualanResource\Pages\ListLaporanPenjualan\Widgets;

use App\Models\Penjualan;
use App\Models\ProdukPenjualan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Total sales for current month
        $currentMonthSales = Penjualan::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah');

        // Total sales for previous month
        $previousMonthSales = Penjualan::whereMonth('tanggal', now()->subMonth()->month)
            ->whereYear('tanggal', now()->subMonth()->year)
            ->sum('jumlah');

        // Calculate growth percentage
        $growthPercentage = 0;
        if ($previousMonthSales > 0) {
            $growthPercentage = (($currentMonthSales - $previousMonthSales) / $previousMonthSales) * 100;
        }

        // Format growth indicator
        $growthIndicator = $growthPercentage >= 0
            ? '+' . number_format($growthPercentage, 1) . '%'
            : number_format($growthPercentage, 1) . '%';

        // Average order value
        $averageOrderValue = Penjualan::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->avg('jumlah') ?? 0;

        // Total number of orders this month
        $totalOrders = Penjualan::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        // Total products sold this month
        $totalProductsSold = ProdukPenjualan::whereHas('penjualan', function ($query) {
            $query->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year);
        })
            ->sum('quantity');

        return [
            Stat::make('Total Penjualan Bulan Ini', 'Rp ' . number_format($currentMonthSales, 0, ',', '.'))
                ->description($growthIndicator . ' dari bulan lalu')
                ->descriptionIcon($growthPercentage >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($growthPercentage >= 0 ? 'success' : 'danger'),

            Stat::make('Rata-rata Nilai Order', 'Rp ' . number_format($averageOrderValue, 0, ',', '.'))
                ->description($totalOrders . ' total transaksi')
                ->descriptionIcon('heroicon-m-shopping-cart'),

            Stat::make('Total Produk Terjual', number_format($totalProductsSold, 0, ',', '.'))
                ->description('Bulan ' . now()->format('F Y'))
                ->descriptionIcon('heroicon-m-cube'),
        ];
    }
}
