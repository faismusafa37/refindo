<?php

namespace App\Filament\Widgets;

use App\Models\Anggaran;
use App\Models\Activity;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BudgetAbsorptionStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false; 

    public static function canView(): bool
    {
    return auth()->user()?->can('view budget absorption');
    }

    protected function getCards(): array
    {
        $projectId = $this->getProjectId();
        
        // Jika tidak ada project_id, kembalikan array kosong
        if (!$projectId) {
            return [];
        }

        $cacheKey = 'budget_stats_'.$projectId.'_'.Auth::id();
        
        $data = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($projectId) {
            $totalBudget = Anggaran::where('project_id', $projectId)->sum('current_amount') ?? 0;
            $totalUsed = Activity::where('project_id', $projectId)->sum('price') ?? 0;
            
            return [
                'total_budget' => $totalBudget,
                'total_used' => $totalUsed,
                'remaining' => $totalBudget - $totalUsed
            ];
        });

        // Jika data tidak valid, kembalikan array kosong
        if (!isset($data['total_budget']) || !isset($data['total_used'])) {
            return [];
        }

        return [
            Card::make('Total Anggaran', 'Rp '.number_format($data['total_budget'], 0, ',', '.'))
                ->description('Jumlah anggaran tersedia')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary'),

            Card::make('Penyerapan Anggaran', 'Rp '.number_format($data['total_used'], 0, ',', '.'))
                ->description('Biaya yang sudah terserap')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('success'),

            Card::make('Sisa Anggaran', 'Rp '.number_format($data['remaining'], 0, ',', '.'))
                ->description('Anggaran yang tersisa')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color($data['remaining'] < 0 ? 'danger' : 'warning')
        ];
    }

    protected function getProjectId()
    {
        $user = Auth::user();
        
        // Untuk DLH, gunakan project_id mereka
        if ($user->hasRole('DLH')) {
            return $user->project_id;
        }
        
        // Untuk non-DLH, gunakan filter jika ada
        return $this->filters['project_id'] ?? null;
    }
}