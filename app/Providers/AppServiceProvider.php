<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use App\Filament\Resources\RoleResource; 



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
        FilamentView::registerRenderHook(
            'panels::body.start',
            fn () => '<link rel="stylesheet" href="' . asset('css/app.css') . '">',

        );
        
        FilamentView::registerRenderHook(
            'panels::body.start',
            fn () => '<div class="custom-background" style="background-image: url(' . asset('images') . ');"></div>'
        );
        
    }
}
