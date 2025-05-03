<?php
namespace App\Filament\Widgets;

use App\Models\Activity;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class RunningActivities extends ChartWidget
{
    protected static ?string $heading = 'Total Distribusi Status Aktivitas';

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
        $user = Auth::user();

        // Base query activity
        $query = Activity::query();

        // Kalau user adalah DLH, filter berdasarkan project_id
        if ($user->hasRole('DLH')) {
            $query->where('project_id', $user->project_id);
        }

        // Hitung jumlah aktivitas berdasarkan status
        $data = [
            'RFU' => (clone $query)->where('status', 'like', '%open%')->count(),
            'on going' => (clone $query)->where('status', 'like', '%in progress%')->count(),
            'selesai' => (clone $query)->where('status', 'like', '%RFU%')->count(),
        ];

        // Menyesuaikan label dan warna berdasarkan status
        $labels = [];
        $dataValues = [];
        $backgroundColors = [];

        $statusLabels = [
            'RFU' => 'Aktivitas yang Terdaftar',
            'on going' => 'On Going',
            'selesai' => 'Finish'
        ];

        $colors = [
            'RFU' => '#FFCE56', // Kuning
            'on going' => '#36A2EB', // Biru
            'selesai' => '#4CAF50', // Hijau
        ];

        foreach ($statusLabels as $key => $label) {
            $labels[] = $label;
            $dataValues[] = $data[$key] ?? 0;
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
