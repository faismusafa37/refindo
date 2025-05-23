<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PartDismantleResource\Pages;
use App\Filament\Resources\PartDismantleResource\RelationManagers;
use App\Models\PartDismantle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ExportAction;
use App\Filament\Exports\PartExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PartDismantleResource extends Resource
{
    protected static ?string $model = PartDismantle::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?string $navigationGroup = 'Inventory Management';

    public static function canAccess(): bool
    {
        return Auth::user()?->can('view part dismantle');
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->can('create part dismantle');
    }

    public static function canView($record): bool
    {
        return Auth::user()?->can('update part dismantle', $record);
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->can('update part dismantle', $record);
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->can('delete part dismantle', $record);
    }


   public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Part Information')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('part_number')
                                ->label('Part Number')
                                ->required()
                                ->maxLength(255),
                                
                            Forms\Components\TextInput::make('no_tiket')
                                ->label('No. Tiket')
                                ->required()
                                ->maxLength(255),
                        ]),
                        
                    Forms\Components\TextInput::make('description')
                        ->label('Description')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                        
                    Forms\Components\TextInput::make('stock_dismantle')
                        ->label('Stock Dismantle')
                        ->required()
                        ->numeric()
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsible(),
        ]);
}
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('No')->sortable(),
                Tables\Columns\TextColumn::make('part_number')->label('Part Number')->searchable(),
                Tables\Columns\TextColumn::make('description')->label('Description'),
                Tables\Columns\TextColumn::make('stock_dismantle')->label('Stock Dismantle'),
                Tables\Columns\TextColumn::make('no_tiket')->label('No. Tiket'),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(PartExporter::class)
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->actions([
                // Hanya Admin yang bisa delete, User tidak bisa
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn () => Auth::user()->hasRole('user')), // Sembunyikan tombol delete untuk User
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartDismantles::route('/'),
            'create' => Pages\CreatePartDismantle::route('/create'),
            'edit' => Pages\EditPartDismantle::route('/{record}/edit'),
        ];
    }
}
