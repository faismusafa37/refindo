<?php
namespace App\Filament\Exports;

use App\Models\StockOpname;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;

class StockOpnameExporter extends Exporter
{
    protected static ?string $model = StockOpname::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('part_number')->label('Part Number'),
            ExportColumn::make('part_name')->label('Part Name'),
            ExportColumn::make('description')->label('Description'),
            ExportColumn::make('stock_in')->label('Stock In'),
            ExportColumn::make('stock_out')->label('Stock Out'),
            ExportColumn::make('final_stock')->label('Final Stock'),
            ExportColumn::make('user.name')->label('User'),
            ExportColumn::make('created_at')->label('Created At'), // Tidak ada format di sini
        ];
    }

    // Fungsi untuk memodifikasi data yang diekspor
    public static function map($record): array
    {
        return [
            'part_number' => $record->part_number,
            'part_name' => $record->part_name,
            'stock_in' => $record->stock_in,
            'stock_out' => $record->stock_out,
            'final_stock' => $record->final_stock,
            'description' => $record->description,
            'user' => $record->user->name, // Nama user
            'created_at' => $record->created_at->format('d M Y H:i'), // Format tanggal di sini
        ];
    }

    public static function getCompletedNotificationBody($export): string
    {
        $body = 'Your stock opname export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
