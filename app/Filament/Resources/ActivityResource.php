<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ActivityExporter;
use App\Filament\Resources\ActivityResource\Pages;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\HtmlColumn;


class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Service Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pekerjaan')
                    ->schema([
                        Forms\Components\TextInput::make('no_unit_tiket')
                            ->label('No Unit/Tiket')
                            ->required(),
                        Forms\Components\TextInput::make('job_description')
                            ->label('Job Description')
                            ->required(),
                        Forms\Components\Textarea::make('task_description')
                            ->label('Task Description')
                            ->required(),
                            Forms\Components\TextInput::make('category_issues')
                            ->label('Category Issues')
                            ->required(),
                            Forms\Components\TextInput::make('priority')
                            ->label('Priority')
                            ->required(),
                        Forms\Components\TextInput::make('pic_assignee')
                            ->label('PIC Assignee')
                            ->required(),
                            Forms\Components\DateTimePicker::make('time_in')
                            ->label('Time In')
                            ->required(),
                            Forms\Components\DateTimePicker::make('time_out')
                            ->label('Time Out')
                            ->required(),
                            Forms\Components\TextInput::make('hour_meter')
                                ->label('Hour Meter (KM Mobil)')
                                ->numeric()
                                ->required(),
                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Data Stock')
                    ->schema([
                        Forms\Components\TextInput::make('part_number')
                            ->label('Part Number')
                            ->required(),
                        Forms\Components\TextInput::make('part_name')
                            ->label('Part Name')
                            ->required(),
                        Forms\Components\Textarea::make('part_description')
                            ->label('Part Description'),
                        Forms\Components\TextInput::make('stock_in')
                            ->label('Stock In')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('stock_out')
                            ->label('Stock Out')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('price_stock')
                            ->label('Price')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\TextInput::make('final_stock')
                            ->label('Final Stock')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Upload Dokumen & Foto')
                    ->description('Unggah dokumentasi pekerjaan maksimal 2MB per file')
                    ->schema([
                        Forms\Components\FileUpload::make('photo_1')
                            ->image()
                            ->maxSize(2048)
                            ->directory('uploads/activities')
                            ->preserveFilenames()
                            ->live(),
                        Forms\Components\FileUpload::make('photo_2')
                            ->image()
                            ->maxSize(2048)
                            ->directory('uploads/activities')
                            ->preserveFilenames()
                            ->live(),
                        Forms\Components\FileUpload::make('photo_3')
                            ->image()
                            ->maxSize(2048)
                            ->directory('uploads/activities')
                            ->preserveFilenames()
                            ->live(),
                        Forms\Components\FileUpload::make('bast_document')
                            ->label('BAST Document')
                            ->maxSize(2048)
                            ->directory('uploads/documents')
                            ->preserveFilenames()
                            ->live(),
                    ]),

                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => Auth::check() ? Auth::id() : null)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_unit_tiket')->label('No Unit/Tiket')->searchable(),
                Tables\Columns\TextColumn::make('job_description')->label('Job Description')->searchable(),
                Tables\Columns\TextColumn::make('category_issues')->label('Category Issues')->sortable(),
                Tables\Columns\TextColumn::make('priority')->label('Priority')->sortable(),
                Tables\Columns\TextColumn::make('pic_assignee')->label('PIC Assignee')->searchable(),
                Tables\Columns\TextColumn::make('time_in')->label('Time In')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('time_out')->label('Time Out')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('hour_meter')->label('Hour Meter')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->sortable(),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('idr')->sortable(false),
                Tables\Columns\TextColumn::make('part_number')->label('Part Number')->sortable(),
                Tables\Columns\TextColumn::make('part_name')->label('Part Name')->sortable(),
                Tables\Columns\TextColumn::make('stock_in')->label('Stock In')->sortable(),
                Tables\Columns\TextColumn::make('stock_out')->label('Stock Out')->sortable(),
                Tables\Columns\TextColumn::make('final_stock')->label('Final Stock')->sortable(),

               Tables\Columns\ImageColumn::make('photo_1')->label('Photo 1')
    ->url(fn ($record) => $record->photo_1 ? asset('storage/'.$record->photo_1) : null)
    ->openUrlInNewTab()
    ->extraAttributes(['target' => '_blank']), // Membuka gambar di tab baru

Tables\Columns\ImageColumn::make('photo_2')->label('Photo 2')
    ->url(fn ($record) => $record->photo_2 ? asset('storage/'.$record->photo_2) : null)
    ->openUrlInNewTab()
    ->extraAttributes(['target' => '_blank']), // Membuka gambar di tab baru

Tables\Columns\ImageColumn::make('photo_3')->label('Photo 3')
    ->url(fn ($record) => $record->photo_3 ? asset('storage/'.$record->photo_3) : null)
    ->openUrlInNewTab()
    ->extraAttributes(['target' => '_blank']), // Membuka gambar di tab baru

           Tables\Columns\TextColumn::make('bast_document')
            ->label('BAST Document')
            ->formatStateUsing(fn ($state) => 'Download')
            ->url(fn ($record) => $record && $record->bast_document
                ? asset('storage/'. $record->bast_document)
                : null)
            ->openUrlInNewTab()
            ->icon('heroicon-o-arrow-down-tray')
            ->color('primary')
            ->extraAttributes(['download' => '']),

                Tables\Columns\TextColumn::make('user.name')->label('User')->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                ->label('Download CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->exporter(ActivityExporter::class),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
