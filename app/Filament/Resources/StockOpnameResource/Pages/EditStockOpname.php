<?php

namespace App\Filament\Resources\StockOpnameResource\Pages;

use App\Filament\Resources\StockOpnameResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockOpname extends EditRecord
{
    protected static string $resource = StockOpnameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
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
