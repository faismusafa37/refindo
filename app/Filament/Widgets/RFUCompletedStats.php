<?php
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class RFUCompletedStats extends BaseWidget
{
    // Mengatur visibilitas widget berdasarkan role
    public static function canView(): bool
    {
        // Hanya Admin dan User yang bisa melihat widget ini, DLH tidak bisa
        return !Auth::user()->hasRole('DLH');
    }

    // Mendapatkan data untuk menampilkan kartu statistik
    protected function getCards(): array
    {
        $user = Auth::user();

        // Base query activity
        $query = Activity::query();

        // Kalau user adalah DLH, filter berdasarkan project_id
        if ($user->hasRole('DLH')) {
            $query->where('project_id', $user->project_id);
        }

        return [
            // Statistik untuk total aktivitas dengan status 'RFU'
            Card::make('Total Activities', (clone $query)->where('status', 'like', '%RFU%')->count())
                ->description('Jumlah seluruh aktivitas yang terdaftar dengan status RFU')
                ->color('primary')
                ->url(route('filament.admin.resources.activities.index', [
                    'tableFilters[status][value]' => 'RFU'  // Filter untuk status 'RFU'
                ])),

            // Statistik untuk aktivitas yang sedang berjalan ('on going')
            Card::make('On Going', (clone $query)->where('status', 'like', '%on going%')->count())
                ->description('Aktivitas yang sedang berjalan')
                ->color('warning')
                ->url(route('filament.admin.resources.activities.index', [
                    'tableFilters[status][value]' => 'ongoing'  // Filter untuk status 'on going'
                ])),

            // Statistik untuk aktivitas yang sudah selesai ('selesai')
            Card::make('Selesai', (clone $query)->where('status', 'like', '%selesai%')->count())
                ->description('Aktivitas yang sudah selesai')
                ->color('success')
                ->url(route('filament.admin.resources.activities.index', [
                    'tableFilters[status][value]' => 'complete'  // Filter untuk status 'selesai'
                ])),
        ];
    }
}
