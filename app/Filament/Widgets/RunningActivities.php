<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use Filament\Widgets\ChartWidget;

class RunningActivities extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Status Aktivitas';

    protected function getType(): string
    {
        return 'pie'; // Ganti jadi pie chart
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        $data = Activity::selectRaw("status, COUNT(*) as count")
            ->whereIn('status', ['RFU', 'in-progress', 'completed', 'waiting-for-parts'])
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'labels' => ['RFU', 'In Progress', 'Completed', 'Waiting for Parts'],
            'datasets' => [
                [
                    'label' => 'Jumlah Aktivitas',
                    'data' => [
                        $data['RFU'] ?? 0,
                        $data['in-progress'] ?? 0,
                        $data['completed'] ?? 0,
                        $data['waiting-for-parts'] ?? 0,
                    ],
                    'backgroundColor' => ['#FFCE56', '#36A2EB', '#4CAF50', '#FF6384'],
                ],
            ],
        ];
    }
}
