<?php

namespace App\Filament\Resources\LaporanPenjualanResource\Pages;

use App\Filament\Resources\LaporanPenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\ProdukPenjualan;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ListLaporanPenjualan extends ListRecords
{
    protected static string $resource = LaporanPenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action needed for reports
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ListLaporanPenjualan\Widgets\SalesOverview::class,
            ListLaporanPenjualan\Widgets\NetProfitOverview::class,
            ListLaporanPenjualan\Widgets\TopSellingProducts::class,
        ];
    }
}
