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
use Illuminate\Support\Facades\Log;

class AnggaranResource extends Resource
{
    protected static ?string $model = Anggaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Finance';

    // Navigation visibility - check permissions
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view anggaran'); // Hanya muncul jika punya akses resource
    }

    public static function canViewAny(): bool
    {
    return auth()->user()?->can('view anggaran');
    }
    // Resource access control
    public static function canAccess(): bool
    {
        return auth()->user()?->can('view anggaran');
    }

    // Record viewing permission
    public static function canView($record): bool
    {
        $user = auth()->user();
        
        // DLH hanya bisa melihat project mereka
        if ($user->hasRole('dlh')) {
            return $record->project_id === $user->project_id;
        }
        
        // Admin dan User bisa melihat semua
        return $user->hasRole(['admin', 'user']);
    }

    // Record editing permission
    public static function canEdit($record): bool
    {
        return auth()->user()?->can('update anggaran');
    }

    // Record deletion permission
    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete anggaran');
    }

    // Record creation permission
    public static function canCreate(): bool
    {
        return Auth::user()?->can('create anggaran');
    }

public static function form(Form $form): Form
{
    $user = auth()->user();
    $projectOptions = Project::query();
    
    if ($user->hasRole('dlh') && $user->project_id) {
        $projectOptions->where('id', $user->project_id);
    }
    
    return $form->schema([
        Section::make('Data Anggaran')->schema([
            Select::make('project_id')
                ->label('Wilayah / Project')
                ->options($projectOptions->pluck('name', 'id'))
                ->required()
                ->disabled($user->hasRole('dlh')),
                    
                TextInput::make('current_amount')
                    ->label('Nilai Anggaran Saat Ini')
                    ->numeric()
                    ->required(),
            ])
        ]);
    }

    public static function table(Table $table): Table
{
    $user = auth()->user();
    
    return $table
        ->query(function () use ($user) {
            $query = Anggaran::query()->with(['project', 'histories']);
            
            // Jika user memiliki project_id DAN bukan admin/superadmin
            if ($user->project_id && !$user->hasAnyRole(['admin', 'user'])) {
                $query->where('project_id', $user->project_id);
            }
            
            return $query;
        })
        ->columns([
            TextColumn::make('project.name')
                ->label('Wilayah')
                ->hidden($user->hasRole('dlh')),
                
            TextColumn::make('current_amount')
                ->label('Anggaran Saat Ini')
                ->money('IDR', true),
                
            TextColumn::make('updated_at')
                ->label('Dibuat Tanggal/Waktu')
                ->dateTime(),

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
                EditAction::make()->hidden(fn($record) => !static::canEdit($record)),
                DeleteAction::make()->hidden(fn($record) => !static::canDelete($record)),
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