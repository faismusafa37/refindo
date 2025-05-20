<?php

namespace App\Filament\Widgets;

use App\Models\Anggaran;
use App\Models\Project;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;

class BudgetStats extends TableWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Anggaran Tahun';

    public static function canView(): bool
    {
    return auth()->user()?->can('view budget stats');
    }


    public function getTableQuery(): Builder
    {
        $query = Anggaran::query()->with('project');
        
        // Jika user memiliki project_id (DLH atau yang terasosiasi dengan project)
        if (Auth::user()->project_id) {
            $query->where('project_id', Auth::user()->project_id);
        }
        
        return $query;
    }

    protected function getTableColumns(): array
    {
        $columns = [
            TextColumn::make('project.name')
            ->label('Wilayah')
            ->wrap(),

            TextColumn::make('current_amount')
                ->label('Anggaran Saat Ini')
                ->money('IDR', true),

            TextColumn::make('updated_at')
                ->label('Diberikan Pada')
                ->dateTime('d M Y'),
        ];

        // Hanya tampilkan kolom project jika user bisa melihat semua project
        if (Auth::user()->can('view all projects')) {
            array_unshift($columns, 
                TextColumn::make('project.name')
                    ->label('Wilayah / Project')
                    ->wrap()
            );
        }

        return $columns;
    }

    protected function getTableFilters(): array
{
    $filters = [
        Filter::make('created_at')
            ->label('Dibuat Pada')
            ->form([
                Select::make('year')
                    ->label('Tahun')
                    ->options(
                        collect(range(now()->year, now()->year - 5))
                            ->mapWithKeys(fn ($y) => [$y => $y])
                            ->toArray()
                    ),
                Select::make('month')
                    ->label('Bulan')
                    ->options([
                        '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
                        '4' => 'April', '5' => 'Mei', '6' => 'Juni',
                        '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                    ]),
            ])
            ->query(function (Builder $query, array $data) {
                if (!empty($data['year'])) {
                    $query->whereYear('updated_at', $data['year']);
                }

                if (!empty($data['month'])) {
                    $query->whereMonth('updated_at', $data['month']);
                }

                return $query;
            }),
    ];

    // Hanya tampilkan filter project jika user bisa melihat semua project DAN tidak memiliki project_id
    if (Auth::user()->can('view all projects') && !Auth::user()->project_id) {
        $filters[] = SelectFilter::make('project_id')
            ->label('Wilayah / Project')
            ->options(Project::pluck('name', 'id'));
    }

    return $filters;
}
}