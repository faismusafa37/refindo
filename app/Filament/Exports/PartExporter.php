<?php

namespace App\Filament\Exports;

use App\Models\PartDismantle;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PartExporter extends Exporter
{
    protected static ?string $model = PartDismantle::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('No'),
            ExportColumn::make('part_number')->label('Part Number'),
            ExportColumn::make('description')->label('Description'),
            ExportColumn::make('stock_dismantle')->label('Stock Dismantle'),
            ExportColumn::make('no_tiket')->label('No. Tiket'),
            ExportColumn::make('desc')->label('Desc'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your part dismantle export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
