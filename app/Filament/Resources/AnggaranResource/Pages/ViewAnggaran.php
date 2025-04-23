<?php

namespace App\Filament\Resources\AnggaranResource\Pages;

use App\Filament\Resources\AnggaranResource;
use App\Models\AnggaranHistory;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Pages\Actions\ButtonAction;
use Illuminate\Database\Eloquent\Builder;

class ViewAnggaran extends ViewRecord implements Tables\Contracts\HasTable
{
    protected static string $resource = AnggaranResource::class;

    use Tables\Concerns\InteractsWithTable;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali ke Daftar')
                ->color('primary')
                ->url(fn () => route('filament.admin.resources.anggarans.index'))
                ->icon('heroicon-o-arrow-left'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return AnggaranHistory::query()
            ->where('anggaran_id', $this->record->id)
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('previous_amount')->label('Nilai Sebelumnya')->money('IDR', true),
            TextColumn::make('current_amount')->label('Nilai Sekarang')->money('IDR', true),
            TextColumn::make('changed_at')->label('Tanggal Perubahan')->dateTime(),
        ];
    }

    protected function getTableHeading(): string
    {
        return 'Riwayat Perubahan Anggaran';
    }

}
