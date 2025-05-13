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
            Card::make('Total Activities', (clone $query)->count())
                ->description('Jumlah seluruh aktivitas yang terdaftar')
                ->color('primary')
                ->url(route('filament.admin.resources.activities.index', [
                    'tableFilters' => [
                        'status_group' => [
                            'value' => 'all',
                            'isActive' => true,
                        ],
                    ],
                ])),

            Card::make('On Going', (clone $query)->whereIn('status', ['in progress', 'pending'])->count())
                ->description('Aktivitas yang sedang berjalan atau pending')
                ->color('warning')
                ->url(route('filament.admin.resources.activities.index', [
                    'tableFilters' => [
                        'status_group' => [
                            'value' => 'on_going',
                            'isActive' => true,
                        ],
                    ],
                ])),

            Card::make('RFU', (clone $query)->where('status', 'like', '%RFU%')->count())
                ->description('Aktivitas yang berstatus RFU')
                ->color('success')
                ->url(route('filament.admin.resources.activities.index', [
                    'tableFilters' => [
                        'status_group' => [
                            'value' => 'rfu',
                            'isActive' => true,
                        ],
                    ],
                ])),
        ];
    }
}