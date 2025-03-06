<?php

namespace App\Filament\Widgets;

use App\Models\Pengeluaran;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;


class ExpenseChart extends ChartWidget
{
    protected static ?string $heading = 'Pengeluaran';
    protected static ?int $sort = 2;
    public ?string $filter = 'today';  // Add default filter value
    protected static string $color = 'danger';

    protected function getData(): array
    {
        $activeFilter = $this->filter ?? 'today';  // Add fallback value if $filter is null

        $dateRange = match ($activeFilter) {
            'today' => [
                'start' => now()->startOfDay(),
                'end' => now()->endOfDay(),
                'period' => 'perHour'
            ],
            'week' => [
                'start' => now()->startOfWeek(),
                'end' => now()->endOfWeek(),
                'period' => 'perDay'
            ],
            'month' => [
                'start' => now()->startOfMonth(),
                'end' => now()->endOfMonth(),
                'period' => 'perDay'
            ],
            'year' => [
                'start' => now()->startOfYear(),
                'end' => now()->endOfYear(),
                'period' => 'perMonth'
            ],
            default => [  // Add default case to handle any other values
                'start' => now()->startOfDay(),
                'end' => now()->endOfDay(),
                'period' => 'perHour'
            ]
        };

        $query = Trend::query(
            Pengeluaran::query()
                ->whereBetween('tanggal', [
                    $dateRange['start']->format('Y-m-d H:i:s'),
                    $dateRange['end']->format('Y-m-d H:i:s')
                ])
        )
            ->between(
                start: $dateRange['start'],
                end: $dateRange['end'],
            )
            ->dateColumn('tanggal');

        if ($dateRange['period'] === 'perHour') {
            $data = $query->perHour();
        } elseif ($dateRange['period'] === 'perDay') {
            $data = $query->perDay();
        } else {
            $data = $query->perMonth();
        }

        $data = $data->sum('jumlah');

        $labels = $data->map(function (TrendValue $value) use ($dateRange) {
            $date = Carbon::parse($value->date);

            if ($dateRange['period'] === 'perHour') {
                return $date->format('H:i');
            } elseif ($dateRange['period'] === 'perDay') {
                return $date->format('d M');
            }
            return $date->format('M Y');
        });

        // Define color based on the static property
        $chartColor = static::$color === 'danger' ? '#ef4444' : '#10b981'; // Red for danger, green otherwise

        return [
            'datasets' => [
                [
                    'label' => 'Pengeluaran ' . ($this->getFilters()[$activeFilter] ?? 'Hari Ini'),
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'backgroundColor' => $chartColor,
                    'borderColor' => $chartColor,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
