<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockOpnameResource\Pages;
use App\Models\StockOpname;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ExportAction;
use App\Filament\Exports\StockOpnameExporter; // Import StockOpnameExporter
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;

class StockOpnameResource extends Resource
{
    protected static ?string $model = StockOpname::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Inventory Management';
    protected static ?string $navigationLabel = 'Stock Opname';
    protected static ?string $pluralLabel = 'Stock Opname';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('part_number')
                    ->label('Part Number')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('part_name')
                    ->label('Part Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('stock_in')
                    ->label('Stock In')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('stock_out')
                    ->label('Stock Out')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('final_stock')
                    ->label('Final Stock')
                    ->numeric()
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->maxLength(500),

                // Komponen Hidden untuk otomatis mengisi user_id
                Hidden::make('user_id')
                    ->default(fn () => Auth::check() ? Auth::id() : null)
                    ->required(), // Jika perlu di-set sebagai required
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('part_number')
                    ->label('Part Number')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('part_name')
                    ->label('Part Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('stock_in')
                    ->label('Stock In')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_out')
                    ->label('Stock Out')
                    ->sortable(),

                Tables\Columns\TextColumn::make('final_stock')
                    ->label('Final Stock')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(StockOpnameExporter::class) // Set exporter class
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->filters([/* Tambahkan filter jika diperlukan */])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockOpnames::route('/'),
            'create' => Pages\CreateStockOpname::route('/create'),
            'edit' => Pages\EditStockOpname::route('/{record}/edit'),
        ];
    }
}
