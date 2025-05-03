<?php

namespace App\Filament\Widgets;

use App\Models\Anggaran;
use App\Models\Activity;
use App\Models\Project;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BudgetAbsorptionStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;
    protected static ?int $sort = -1;

    public static function canView(): bool
    {
        $can = auth()->check() && auth()->user()->hasAnyRole(['admin', 'user']);
        Log::info('canView BudgetAbsorptionStats', ['can' => $can]);
        return $can;
    }

    protected function hasFiltersForm(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'user']);
    }

    public function filtersForm(Form $form): Form
    {
        if (!auth()->user()->hasAnyRole(['admin', 'user'])) {
            return $form;
        }

        return $form->schema([
            Select::make('project_id')
                ->label('Pilih Wilayah')
                ->options(function () {
                    return Cache::remember('projects_select_options', 60, function () {
                        return Project::pluck('name', 'id');
                    });
                })
                ->placeholder('-- Pilih Wilayah --')
                ->reactive()
                ->live(),
        ]);
    }

    protected function getCards(): array
    {
        $user = Auth::user();
        Log::info('getCards triggered by user', [
            'user_id' => $user->id,
            'roles' => $user->getRoleNames(),
        ]);

        if ($user->hasAnyRole(['admin', 'user'])) {
            $projectId = $this->filters['project_id'] ?? null;

            if (!$projectId) {
                Log::info('No project selected. Cards will not show.');
                return [];
            }

            $cacheKey = 'budget_stats_' . $projectId;

            [$totalBudget, $totalUsed, $remaining] = Cache::remember($cacheKey, 60, function () use ($projectId) {
                $totalBudget = Anggaran::where('project_id', $projectId)->sum('current_amount');
                $totalUsed = Activity::where('project_id', $projectId)->sum('price');
                $remaining = $totalBudget - $totalUsed;

                return [$totalBudget, $totalUsed, $remaining];
            });

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

        Log::info('User tidak punya akses melihat cards.');
        return [];
    }
}
