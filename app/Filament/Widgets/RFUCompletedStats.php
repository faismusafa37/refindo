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
            Card::make('Total Activities', Activity::where('status','like','%RFU%')->count())
                ->description('Jumlah seluruh aktivitas yang terdaftar')
                ->color('primary')
                ->url(route('filament.admin.resources.activities.index')),

            Card::make('On Going', Activity::where('status', 'like', '%on going%')->count())
                ->description('Aktivitas yang sedang berjalan')
                ->color('warning')
                ->url(route('filament.admin.resources.activities.index', [
                    'tableFilters[status][value]' => 'ongoing'
                ])),

            Card::make('Selesai', Activity::where('status', 'like', '%selesai%')->count())
                ->description('Aktivitas yang sudah selesai')
                ->color('success') // Warna hijau
                ->url(route('filament.admin.resources.activities.index', [
                    'tableFilters[status][value]' => 'complet'
                ]))

        ];
    }
}
