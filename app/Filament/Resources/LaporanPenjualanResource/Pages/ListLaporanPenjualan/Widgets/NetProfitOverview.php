<?php

namespace App\Filament\Resources\LaporanPenjualanResource\Pages\ListLaporanPenjualan\Widgets;

use App\Models\Penjualan;
use App\Models\ProdukPenjualan;
use Filament\Forms;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NetProfitOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getFilters(): ?array
    {
        return [
            'period' => Forms\Components\Select::make('period')
                ->label('Periode')
                ->options([
                    'today' => 'Hari Ini',
                    'week' => 'Minggu Ini',
                    'month' => 'Bulan Ini',
                    'year' => 'Tahun Ini',
                ])
                ->default('month'),
            'kategori' => Forms\Components\Select::make('kategori')
                ->label('Kategori')
                ->relationship('kategori', 'nama')
                ->searchable()
                ->preload(),
        ];
    }

    protected function getStats(): array
    {
        $query = ProdukPenjualan::query()
            ->join('penjualans', 'produk_penjualans.penjualan_id', '=', 'penjualans.id')
            ->join('produks', 'produk_penjualans.produk_id', '=', 'produks.id');

        // Apply period filter
        switch ($this->filterFormData['period'] ?? 'month') {
            case 'today':
                $query->whereDate('penjualans.tanggal', Carbon::today());
                $previousQuery = clone $query;
                $previousQuery->whereDate('penjualans.tanggal', Carbon::yesterday());
                $periodLabel = 'hari ini';
                $comparisonLabel = 'kemarin';
                break;
            case 'week':
                $query->whereBetween('penjualans.tanggal', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                $previousQuery = clone $query;
                $previousQuery->whereBetween('penjualans.tanggal', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
                $periodLabel = 'minggu ini';
                $comparisonLabel = 'minggu lalu';
                break;
            case 'year':
                $query->whereYear('penjualans.tanggal', Carbon::now()->year);
                $previousQuery = clone $query;
                $previousQuery->whereYear('penjualans.tanggal', Carbon::now()->subYear()->year);
                $periodLabel = 'tahun ini';
                $comparisonLabel = 'tahun lalu';
                break;
            default: // month
                $query->whereMonth('penjualans.tanggal', Carbon::now()->month)
                    ->whereYear('penjualans.tanggal', Carbon::now()->year);
                $previousQuery = clone $query;
                $previousQuery->whereMonth('penjualans.tanggal', Carbon::now()->subMonth()->month)
                    ->whereYear('penjualans.tanggal', Carbon::now()->subMonth()->year);
                $periodLabel = 'bulan ini';
                $comparisonLabel = 'bulan lalu';
        }

        // Apply category filter
        if (isset($this->filterFormData['kategori'])) {
            $query->where('produks.kategori_id', $this->filterFormData['kategori']);
            $previousQuery->where('produks.kategori_id', $this->filterFormData['kategori']);
        }

        // Calculate current period net profit
        $currentNetProfit = $query->sum(DB::raw('(produks.harga_jual_toko - produks.harga_beli) * produk_penjualans.quantity'));

        // Calculate previous period net profit
        $previousNetProfit = $previousQuery->sum(DB::raw('(produks.harga_jual_toko - produks.harga_beli) * produk_penjualans.quantity'));

        // Calculate growth percentage
        $growthPercentage = 0;
        if ($previousNetProfit > 0) {
            $growthPercentage = (($currentNetProfit - $previousNetProfit) / $previousNetProfit) * 100;
        }

        // Format growth indicator
        $growthIndicator = $growthPercentage >= 0
            ? '+' . number_format($growthPercentage, 1) . '%'
            : number_format($growthPercentage, 1) . '%';

        // Calculate average daily profit
        $daysInPeriod = match ($this->filterFormData['period'] ?? 'month') {
            'today' => 1,
            'week' => 7,
            'month' => Carbon::now()->daysInMonth,
            'year' => Carbon::now()->daysInYear,
        };

        $averageDailyProfit = $currentNetProfit / $daysInPeriod;

        return [
            Stat::make('Laba Bersih ' . ucfirst($periodLabel), 'Rp ' . number_format($currentNetProfit, 0, ',', '.'))
                ->description($growthIndicator . ' dari ' . $comparisonLabel)
                ->descriptionIcon($growthPercentage >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($growthPercentage >= 0 ? 'success' : 'danger'),

            Stat::make('Rata-rata Laba per Hari', 'Rp ' . number_format($averageDailyProfit, 0, ',', '.'))
                ->description('Berdasarkan ' . $periodLabel)
                ->descriptionIcon('heroicon-m-calculator'),

            Stat::make('Margin Laba', number_format(($currentNetProfit / ($query->sum(DB::raw('produks.harga_jual_toko * produk_penjualans.quantity')) ?: 1)) * 100, 1) . '%')
                ->description('Dari total penjualan ' . $periodLabel)
                ->descriptionIcon('heroicon-m-banknotes'),
        ];
    }
}
