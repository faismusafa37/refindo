<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class RFUCompletedStats extends BaseWidget
{
    protected function getCards(): array
    {
        $user = Auth::user();
        $query = Activity::query();

        if ($user->hasRole('DLH')) {
            $query->where('project_id', $user->project_id);
        }

        return [
            Card::make('Total Activities', $query->count())
                ->description('Jumlah seluruh aktivitas yang terdaftar')
                ->color('primary')
                ->url(route('filament.admin.resources.activities.index')),

            Card::make('On Going', $query->clone()->where('status', 'in progress')->count())
                ->description('Aktivitas yang sedang berjalan')
                ->color('warning')
                ->url(route('filament.admin.resources.activities.index', [
                    'tableFilters' => [
                        'status' => [
                            'value' => 'in progress',
                            'isActive' => true,
                        ],
                    ],
                ])),

            Card::make('Selesai', $query->clone()->where('status', 'RFU')->count())
                ->description('Aktivitas yang berstatus RFU')
                ->color('success')
                ->url(route('filament.admin.resources.activities.index', [
                    'tableFilters' => [
                        'status' => [
                            'value' => 'RFU',
                            'isActive' => true,
                        ],
                    ],
                ])),
        ];
    }
}