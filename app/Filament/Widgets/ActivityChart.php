<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Activity;
use Carbon\Carbon;

class ActivityChart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah Perbaikan Per Bulan';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $weeklyData = Activity::whereBetween('created_at', [Carbon::today()->startOfWeek(), Carbon::today()->endOfWeek()])->count();
        $monthlyData = Activity::whereBetween('created_at', [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()])->count();
        $yearlyData = Activity::whereBetween('created_at', [Carbon::today()->startOfYear(), Carbon::today()->endOfYear()])->count();

        return [
            'labels' => ['Minggu Ini', 'Bulan Ini', 'Tahun Ini'],
            'datasets' => [
                [
                    'label' => 'Jumlah Aktivitas',
                    'data' => [$weeklyData, $monthlyData, $yearlyData],
                    'backgroundColor' => ['#4A90E2', '#50E3C2', '#F5A623'],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie'; 
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
