<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnggaranResource\Pages;
use App\Models\Anggaran;
use App\Models\Project;
use App\Models\AnggaranHistory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\{ViewAction, EditAction, DeleteAction, DeleteBulkAction};
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AnggaranResource extends Resource
{
    protected static ?string $model = Anggaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Finance';

    // Hanya tampil di sidebar jika role Admin atau User
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        return $user->hasRole(['admin', 'user']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Data Anggaran')
                ->schema([
                    Select::make('project_id')
                        ->label('Wilayah / Project')
                        ->options(Project::pluck('name', 'id'))
                        ->required(),

                    TextInput::make('current_amount')
                        ->label('Nilai Anggaran Saat Ini')
                        ->numeric()
                        ->required(),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('project.name')->label('Wilayah'),
            TextColumn::make('current_amount')->label('Anggaran Saat Ini')->money('IDR', true),
            TextColumn::make('updated_at')->label('Dibuat Tanggal/Waktu')->dateTime(),

            TextColumn::make('histories')
                ->label('History Perubahan')
                ->getStateUsing(function ($record) {
                    $lastHistory = $record->histories()->latest()->first();
                    if ($lastHistory) {
                        $previousAmount = number_format($lastHistory->previous_amount, 0, ',', '.');
                        $currentAmount = number_format($lastHistory->current_amount, 0, ',', '.');
                        $changedAt = $lastHistory->changed_at
                            ? Carbon::parse($lastHistory->changed_at)->format('d-m-Y H:i')
                            : 'Tanggal tidak tersedia';

                        return "Sebelumnya: IDR {$previousAmount} → Sekarang: IDR {$currentAmount} pada {$changedAt}";
                    }
                    return 'Belum ada perubahan';
                }),
        ])
        ->actions([
            ViewAction::make(),
            EditAction::make(),
        ])
        ->bulkActions([
            DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnggarans::route('/'),
            'create' => Pages\CreateAnggaran::route('/create'),
            'edit' => Pages\EditAnggaran::route('/{record}/edit'),
            'view' => Pages\ViewAnggaran::route('/{record}'),
        ];
    }

    // Simpan history perubahan current_amount
    public static function boot(): void
    {
        parent::boot();

        static::updated(function ($anggaran) {
            if ($anggaran->isDirty('current_amount')) {
                AnggaranHistory::create([
                    'anggaran_id' => $anggaran->id,
                    'previous_amount' => $anggaran->getOriginal('current_amount'),
                    'current_amount' => $anggaran->current_amount,
                    'changed_at' => now(),
                ]);
            }
        });
    }
}
