<?php
namespace App\Filament\Widgets;

use App\Models\StockOpname;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Facades\Auth;

class StockOpnameView extends TableWidget
{
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->can('view stock opname');
    }
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

    public static function shouldDisplay(): bool
    {
        $user = auth()->user();

        // Daftar project DLH
        $dlhProjects = [
            'UPST Bantar Gebang (Dinas LH)',
            'Sudin Selatan (Dinas LH)',
            'Sudin Barat (Dinas LH)',
            'Sudin Pusat (Dinas LH)',
        ];

        // Jika user tidak login = sembunyikan widget
        if (! $user) {
            return false;
        }

        // Sembunyikan widget jika role user adalah 'DLH' atau jika user terkait dengan project DLH
        if ($user->hasRole('DLH')) {
            // Cek apakah project user terkait ada di daftar project DLH
            $userProjects = $user->projects->pluck('name')->toArray();
            if (array_intersect($userProjects, $dlhProjects)) {
                return false;
            }
        }

        // Pastikan hanya ditampilkan jika pengguna memiliki permission 'view stock opname'
        return $user->can('view stock opname');
    }
}
