<?php

namespace App\Filament\Exports;

use App\Models\Activity;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ActivityExporter extends Exporter
{
    protected static ?string $model = Activity::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('no_unit_tiket')->label('No/Unit Tiket'),
            ExportColumn::make('job_description')->label('Job Description'),
            ExportColumn::make('task_description')->label('Task Description'),
            ExportColumn::make('category_issues')->label('Category Issue'),
            ExportColumn::make('priority')->label('Priority'),
            ExportColumn::make('pic_assignee')->label('PIC Assignee'),
            ExportColumn::make('time_in')->label('Time In'),
            ExportColumn::make('time_out')->label('Time Out'),
            ExportColumn::make('hour_meter')->label('Hour Meter'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('price')->label('Price')->formatStateUsing(fn ($state) =>'Rp' . number_format($state, 2)),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your activity export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
