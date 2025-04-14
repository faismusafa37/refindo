<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Activity;

class RFUCompletedStats extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total RFU', Activity::whereLike('status', '%rf%')->count())
            ->description('Aktivitas menunggu tindakan')
            ->color('warning'),

            Card::make('Sudah Selesai', Activity::whereLike('status', '%complet%')->count())
            ->description('RFU yang sudah selesai dan aktivitas lainnya')
            ->color('success'),

        ];
    }
}
