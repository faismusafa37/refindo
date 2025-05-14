<?php
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ActivityChart extends ChartWidget
{
     public static function canView(): bool
    {
        return auth()->check() && auth()->user()->can('view activities');
    }
    protected static ?string $heading = 'Total Jumlah Perbaikan Per Bulan';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $user = Auth::user();

        // Base query activity
        $query = Activity::query();

        // Kalau user adalah DLH, filter berdasarkan project_id
        if ($user->hasRole('DLH')) {
            $query->where('project_id', $user->project_id);
        }

        // Hitung jumlah aktivitas untuk minggu ini, bulan ini, dan tahun ini
        $weeklyData = (clone $query)->whereBetween('created_at', [Carbon::today()->startOfWeek(), Carbon::today()->endOfWeek()])->count();

        // Perbaiki query untuk bulan ini
        $monthlyData = (clone $query)->whereBetween('created_at', [
            Carbon::today()->startOfMonth()->startOfDay(),  // Mulai bulan ini
            Carbon::today()->endOfMonth()->endOfDay()       // Akhir bulan ini
        ])->count();

        $yearlyData = (clone $query)->whereBetween('created_at', [Carbon::today()->startOfYear(), Carbon::today()->endOfYear()])->count();

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
