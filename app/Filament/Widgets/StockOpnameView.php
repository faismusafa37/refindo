<?php

namespace App\Filament\Widgets;

use App\Models\StockOpname;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class StockOpnameView extends TableWidget
{
    protected static ?string $heading = 'Stock Movement';

    protected function getTableQuery(): Builder
    {
        return StockOpname::query()->orderBy('part_name');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('part_name')
                ->label('Part')
                ->searchable()
                ->sortable(),

            IconColumn::make('stock_in')
                ->label('Stock In')
                ->icon(fn ($record) => 'heroicon-o-arrow-up')
                ->color('success')
                ->extraAttributes(['style' => 'font-weight: bold'])
                ->tooltip(fn ($record) => $record->stock_in),

            IconColumn::make('stock_out')
                ->label('Stock Out')
                ->icon(fn ($record) => 'heroicon-o-arrow-down')
                ->color('danger')
                ->extraAttributes(['style' => 'font-weight: bold'])
                ->tooltip(fn ($record) => $record->stock_out),

            TextColumn::make('final_stock')
                ->label('Final Stock')
                ->sortable()
                ->formatStateUsing(fn ($state) => number_format($state)),
        ];
    }
}
