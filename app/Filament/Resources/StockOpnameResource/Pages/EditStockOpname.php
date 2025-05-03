<?php

namespace App\Filament\Resources\StockOpnameResource\Pages;

use App\Filament\Resources\StockOpnameResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditStockOpname extends EditRecord
{
    protected static string $resource = StockOpnameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Aksi Delete hanya terlihat oleh Admin
            Actions\DeleteAction::make()
            ->hidden(fn () => Auth::user()->hasRole('user'))
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['final_stock'] = ($data['stock_in'] ?? 0) - ($data['stock_out'] ?? 0);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
