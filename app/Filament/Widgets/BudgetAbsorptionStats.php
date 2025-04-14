<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget;
use App\Models\Activity;

class BudgetAbsorptionStats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $totalBudget = Activity::sum('budget'); // misal ada kolom 'budget'
        $totalUsed = Activity::sum('price');    // biaya yang diserap

        $remaining = $totalBudget - $totalUsed;

        return [
            Card::make('Dari Anggaran', number_format($totalBudget))
                ->description('Total anggaran yang tersedia')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary'),

            Card::make('Penyerapan Anggaran', number_format($totalUsed))
                ->description('Biaya yang sudah terserap')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('success'),

            Card::make('Sisa Anggaran', number_format($remaining))
                ->description('Sisa yang belum diserap')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('warning'),
        ];
    }
}
