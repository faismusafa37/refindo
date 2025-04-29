<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateActivity extends CreateRecord
{
    protected static string $resource = ActivityResource::class;

    public function getActions(): array
    {
        return [
            Action::make('import_excel')
    ->label('Import Excel')
    ->icon('heroicon-o-arrow-up-tray')
    ->url(route('activities.import.form')) // buka halaman form
    ->openUrlInNewTab(false),
        ];
    }
    
}


