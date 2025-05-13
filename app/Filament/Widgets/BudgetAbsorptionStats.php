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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BudgetAbsorptionStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;
    protected static ?int $sort = -1;
    protected static ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'user']);
    }

    protected function hasFiltersForm(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'user']);
    }

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Select::make('project_id')
                ->label('Pilih Wilayah')
                ->options(function () {
                    return Project::orderBy('name')->pluck('name', 'id');
                })
                ->placeholder('-- Pilih Wilayah --')
                ->searchable()
                ->live()
                ->debounce(500)
                ->afterStateUpdated(function () {
                    $this->clearCache();
                }),
        ]);
    }

    protected function getCards(): array
    {
        $projectId = $this->filters['project_id'] ?? null;
        
        // Return empty array if no project selected
        if (!$projectId) {
            return [];
        }

        try {
            $cacheKey = "budget_stats_{$projectId}_" . Auth::id();
            $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($projectId) {
                $budget = Anggaran::where('project_id', $projectId)
                    ->select(DB::raw('COALESCE(SUM(current_amount), 0) as total'))
                    ->first();

                $used = Activity::where('project_id', $projectId)
                    ->select(DB::raw('COALESCE(SUM(price), 0) as total'))
                    ->first();

                return [
                    'total_budget' => $budget->total,
                    'total_used' => $used->total,
                    'remaining' => $budget->total - $used->total
                ];
            });

            return [
                Card::make('Total Anggaran', 'Rp ' . number_format($data['total_budget'], 0, ',', '.'))
                    ->description('Jumlah anggaran tersedia')
                    ->descriptionIcon('heroicon-o-banknotes')
                    ->color('primary'),

                Card::make('Penyerapan Anggaran', 'Rp ' . number_format($data['total_used'], 0, ',', '.'))
                    ->description('Biaya yang sudah terserap')
                    ->descriptionIcon('heroicon-o-chart-bar')
                    ->color('success'),

                Card::make('Sisa Anggaran', 'Rp ' . number_format($data['remaining'], 0, ',', '.'))
                    ->description('Anggaran yang tersisa')
                    ->descriptionIcon('heroicon-o-currency-dollar')
                    ->color($data['remaining'] < 0 ? 'danger' : 'warning'),
            ];
        } catch (\Exception $e) {
            Log::error('Error loading budget stats: ' . $e->getMessage());
            return [
                Card::make('Error', 'Gagal memuat data')
                    ->color('danger')
            ];
        }
    }

    protected function clearCache(): void
    {
        $projectId = $this->filters['project_id'] ?? null;
        if ($projectId) {
            $cacheKey = "budget_stats_{$projectId}_" . Auth::id();
            Cache::forget($cacheKey);
        }
    }
}