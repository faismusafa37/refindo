<?php

namespace App\Filament\Resources\AnggaranResource\Pages;

use App\Filament\Resources\AnggaranResource;
use App\Models\Anggaran;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListAnggarans extends ListRecords
{
    protected static string $resource = AnggaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->hidden(!AnggaranResource::canCreate()),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        
        // Filter hanya untuk DLH yang mengakses via widget
        if (auth()->user()->can('view budget stats') && !auth()->user()->can('view anggaran')) {
            return $query->where('project_id', auth()->user()->project_id);
        }
        
        return $query;
    }
}