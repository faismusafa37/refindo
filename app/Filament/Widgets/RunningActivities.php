<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use Filament\Widgets\ChartWidget;

class RunningActivities extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Status Aktivitas';

    protected function getType(): string
    {
        return 'pie'; // Pie chart
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
        // Ambil jumlah aktivitas berdasarkan status yang ada, dengan menggunakan like
        $data = [
            'RFU' => Activity::where('status', 'like', '%RFU%')->count(),
            'on going' => Activity::where('status', 'like', '%on going%')->count(),
            'selesai' => Activity::where('status', 'like', '%selesai%')->count(),
        ];

        // Menyesuaikan label dan warna berdasarkan status yang ada
        $labels = [];
        $dataValues = [];
        $backgroundColors = [];

        // Keterangan status yang lebih ringkas
        $statusLabels = [
            'RFU' => 'Aktivitas yang Terdaftar',
            'on going' => 'On Going',
            'selesai' => 'Finish'
        ];

        // Warna untuk tiap status
        $colors = [
            'RFU' => '#FFCE56', // Kuning
            'on going' => '#36A2EB', // Biru
            'selesai' => '#4CAF50', // Hijau
        ];

        // Proses data untuk chart
        foreach ($statusLabels as $key => $label) {
            $labels[] = $label;
            $dataValues[] = $data[$key] ?? 0; // Jika tidak ada data, set 0
            $backgroundColors[] = $colors[$key];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Jumlah Aktivitas',
                    'data' => $dataValues,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];
    }
}
