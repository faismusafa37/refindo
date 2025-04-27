<?php

namespace App\Filament;

use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use Illuminate\Support\Str;

class CustomBackgroundPlugin extends FilamentBackgroundsPlugin
{
    public function shouldShowBackground(): bool
    {
        $route = request()->route()?->getName();

        return Str::startsWith($route, 'filament.') && (
            // Halaman login
            Str::contains($route, '.auth.') ||
            // Halaman dashboard
            Str::endsWith($route, '.pages.dashboard')
        );
    }

    public function getImageProvider(): MyImages
    {
        return MyImages::make()
        ->directory('images');
    }
}
