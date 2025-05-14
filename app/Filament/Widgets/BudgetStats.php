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

    // Mengatur visibilitas widget berdasarkan role
     public static function canView(): bool
    {
        return auth()->check() && auth()->user()->can('view anggaran');
    }

    // Mendapatkan query untuk tabel berdasarkan project yang terkait
    public function getTableQuery(): Builder
{
    return Anggaran::query()->with('project');
}

    // Menentukan kolom-kolom yang ditampilkan di tabel
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('project.name')
                ->label('Wilayah / Project')
                ->wrap(),

            TextColumn::make('current_amount')
                ->label('Anggaran Saat Ini')
                ->money('IDR', true),

            TextColumn::make('updated_at')
                ->label('Diberikan Pada')
                ->dateTime('d M Y'),
        ];
    }

    // Menentukan filter-filter yang ditampilkan di tabel
    protected function getTableFilters(): array
    {
        $user = Auth::user();

        return [
            // Filter gabungan "Dibuat Pada" (Tahun dan Bulan)
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

            // Filter project_id untuk Admin dan User
            SelectFilter::make('project_id')
                ->label('Wilayah / Project')
                ->options(Project::pluck('name', 'id'))
                // Menampilkan filter project_id hanya untuk Admin dan User
                ->hidden(! $user->hasRole('admin') && ! $user->hasRole('user')),
        ];
    }
}
