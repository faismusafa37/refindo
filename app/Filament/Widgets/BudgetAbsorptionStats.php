<?php

namespace App\Filament\Widgets;

use App\Models\Anggaran;
use App\Models\Activity;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;

class BudgetAbsorptionStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;
    protected static ?int $sort  = -1;

    protected function getCards(): array
{
    $user = Auth::user();

    // Kalau DLH, jangan tampilkan widget
    if ($user->hasRole('DLH')) {
        return [];
    }

    $userProjectId = $user->project_id;

    // Semua selain DLH (Admin & User) bisa pakai filter
    $projectId = $this->filters['project_id'] ?? $userProjectId;

    $totalBudget = Anggaran::where('project_id', $projectId)->sum('current_amount');
    $totalUsed   = Activity::where('project_id', $projectId)->sum('price');
    $remaining   = $totalBudget - $totalUsed;

    return [
        Card::make('Total Anggaran', 'Rp ' . number_format($totalBudget, 0, ',', '.'))
            ->description('Jumlah anggaran tersedia')
            ->descriptionIcon('heroicon-o-banknotes')
            ->color('primary'),

        Card::make('Penyerapan Anggaran', 'Rp ' . number_format($totalUsed, 0, ',', '.'))
            ->description('Biaya yang sudah terserap')
            ->descriptionIcon('heroicon-o-chart-bar')
            ->color('success'),

        Card::make('Sisa Anggaran', 'Rp ' . number_format($remaining, 0, ',', '.'))
            ->description('Anggaran yang tersisa')
            ->descriptionIcon('heroicon-o-currency-dollar')
            ->color('warning'),
    ];
}

}
