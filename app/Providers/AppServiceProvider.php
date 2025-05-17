<?php

namespace App\Providers;

use App\Filament\Resources\AnggaranResource;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Menambahkan file CSS custom ke halaman dashboard Filament
        FilamentView::registerRenderHook(
            'panels::head.end', // Menambahkan link ke head
            fn () => '<link rel="stylesheet" href="' . asset('css/filament.css') . '">',
        );

        // Menambahkan class khusus ke body di dashboard
        FilamentView::registerRenderHook(
            'panels::body.start', // Menambahkan class ke body
            fn () => '<script>document.body.classList.add("dashboard-page")</script>'
        );

        Filament::serving(function () {
            // Hapus item navigasi untuk DLH
            if (auth()->check() && auth()->user()->hasRole('dlh')) {
                Filament::forgetNavigationItems([
                    AnggaranResource::class,
                ]);
            }
        });
    }
}
