<?php

namespace App\Filament\Exports;

use App\Models\Activity;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Forms\Components\DatePicker;

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
            ExportColumn::make('price')->label('Price')->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 2)),
        ];
    }

    public static function form($form)
    {
        // Menambahkan filter rentang tanggal untuk ekspor
        return $form->schema([
            DatePicker::make('start_date')
                ->label('Start Date')
                ->required(),
            DatePicker::make('end_date')
                ->label('End Date')
                ->required(),
        ]);
    }

    public static function getQuery(array $filters): \Illuminate\Database\Eloquent\Builder
    {
        $query = Activity::query();

        // Filter berdasarkan rentang tanggal yang dipilih
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('time_in', [
                $filters['start_date'],
                $filters['end_date'],
            ]);
        }

        return $query;
    }

    public static function getCompletedNotificationBody($export): string
    {
        $body = 'Your activity export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
