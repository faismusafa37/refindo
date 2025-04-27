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
        // Cek role user, kalau DLH resource ini tidak bisa diakses
        return !Auth::user()?->hasRole('DLH');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('part_number')
                    ->label('Part Number')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('stock_dismantle')
                    ->label('Stock Dismantle')
                    ->required()
                    ->numeric(),

                Forms\Components\TextInput::make('no_tiket')
                    ->label('No. Tiket')
                    ->required()
                    ->maxLength(255),
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
