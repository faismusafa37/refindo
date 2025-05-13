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
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\HtmlColumn;
use Carbon\Carbon;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Log;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Service Management';



    public static function form(Form $form): Form
    {
        $yearLimit = Carbon::now()->subYears(3);

        return $form
            ->schema([
                Forms\Components\Section::make('Data Pekerjaan')
                    ->schema([
                        Forms\Components\TextInput::make('no_unit_tiket')->label('No Unit/Tiket')->required(),
                        Forms\Components\TextInput::make('job_description')->label('Job Description')->required(),
                        Forms\Components\Textarea::make('task_description')->label('Task Description')->required(),
                        Forms\Components\TextInput::make('category_issues')->label('Category Issues')->required(),
                        Forms\Components\TextInput::make('priority')->label('Priority')->required(),
                        Forms\Components\TextInput::make('pic_assignee')->label('PIC Assignee')->required(),
                        Forms\Components\DateTimePicker::make('time_in')->label('Time In')->required(),
                        Forms\Components\DateTimePicker::make('time_out')->label('Time Out')->required(),
                        Forms\Components\TextInput::make('hour_meter')->label('Hour Meter (KM Mobil)')->numeric()->required(),
                        Forms\Components\TextInput::make('status')->label('Status')->required(),
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) =>
                                $set('total_price', ($state ?? 0) + ($get('price_stock') ?? 0))
                            ),
                        Forms\Components\Select::make('project_id')
                            ->label('Project')
                            ->relationship('project', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Data Stock')
                    ->schema([
                        Forms\Components\TextInput::make('part_number')->label('Part Number')->required(),
                        Forms\Components\TextInput::make('part_name')->label('Part Name')->required(),
                        Forms\Components\Textarea::make('part_description')->label('Part Description'),
                        Forms\Components\TextInput::make('stock_in')
                            ->label('Stock In')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $stockOut = $get('stock_out') ?? 0;
                                $set('final_stock', ($state ?? 0) - $stockOut);
                            }),
                        Forms\Components\TextInput::make('stock_out')
                            ->label('Stock Out')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $stockIn = $get('stock_in') ?? 0;
                                $set('final_stock', $stockIn - ($state ?? 0));
                            }),
                        Forms\Components\TextInput::make('price_stock')
                            ->label('Price Stock')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) =>
                                $set('total_price', ($state ?? 0) + ($get('price') ?? 0))
                            ),
                        Forms\Components\TextInput::make('final_stock')
                            ->label('Final Stock')
                            ->numeric()
                            ->required()
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('total_price')
                            ->label('Total Price')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Upload Dokumen & Foto')
                    ->description('Unggah dokumentasi pekerjaan maksimal 10MB per file')
                    ->schema([
                        Forms\Components\FileUpload::make('photo_1')
                            ->label('Foto Before')
                            ->image()
                            ->maxSize(10240)
                            ->directory('uploads/activities')
                            ->preserveFilenames()
                            ->live()
                            ->required(function (callable $get) use ($yearLimit) {
                                $timeIn = $get('time_in');
                                return $timeIn && Carbon::parse($timeIn)->greaterThan($yearLimit);
                            }),

                        Forms\Components\FileUpload::make('photo_2')
                            ->label('Foto On Going')
                            ->image()
                            ->maxSize(10240)
                            ->directory('uploads/activities')
                            ->preserveFilenames()
                            ->live()
                            ->required(function (callable $get) use ($yearLimit) {
                                $timeIn = $get('time_in');
                                return $timeIn && Carbon::parse($timeIn)->greaterThan($yearLimit);
                            }),

                        Forms\Components\FileUpload::make('photo_3')
                            ->label('Foto After')
                            ->image()
                            ->maxSize(10240)
                            ->directory('uploads/activities')
                            ->preserveFilenames()
                            ->live()
                            ->required(function (callable $get) use ($yearLimit) {
                                $timeIn = $get('time_in');
                                return $timeIn && Carbon::parse($timeIn)->greaterThan($yearLimit);
                            }),

                        Forms\Components\FileUpload::make('bast_document')
                            ->label('BAST Document')
                            ->maxSize(10240)
                            ->directory('uploads/documents')
                            ->preserveFilenames()
                            ->live()
                            ->required(function (callable $get) use ($yearLimit) {
                                $timeIn = $get('time_in');
                                return $timeIn && Carbon::parse($timeIn)->greaterThan($yearLimit);
                            }),
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
                Tables\Columns\TextColumn::make('project.name')->label('Project'),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('idr')->sortable(false),
                Tables\Columns\TextColumn::make('price_stock')->label('Price Stock')->money('idr')->sortable(false),
                Tables\Columns\TextColumn::make('total_price')->label('Total Price')->money('idr')->sortable(false),
                Tables\Columns\TextColumn::make('part_number')->label('Part Number')->sortable(),
                Tables\Columns\TextColumn::make('part_name')->label('Part Name')->sortable(),
                Tables\Columns\TextColumn::make('stock_in')->label('Stock In')->sortable(),
                Tables\Columns\TextColumn::make('stock_out')->label('Stock Out')->sortable(),
                Tables\Columns\TextColumn::make('final_stock')->label('Final Stock')->sortable()->getStateUsing(function ($record) {
                    return ($record->stock_in ?? 0) - ($record->stock_out ?? 0);
                }),

                Tables\Columns\ImageColumn::make('photo_1')->label('Foto Before')
                    ->url(fn ($record) => $record->photo_1 ? asset('storage/' . $record->photo_1) : null)
                    ->openUrlInNewTab(),

                Tables\Columns\ImageColumn::make('photo_2')->label('Foto On Going')
                    ->url(fn ($record): string|null => $record->photo_2 ? asset('storage/' . $record->photo_2) : null)
                    ->openUrlInNewTab(),

                Tables\Columns\ImageColumn::make('photo_3')->label('Foto After')
                    ->url(fn ($record) => $record->photo_3 ? asset('storage/' . $record->photo_3) : null)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('bast_document')
                    ->label('BAST Document')
                    ->formatStateUsing(fn ($state) => 'Download')
                    ->url(fn ($record) => $record && $record->bast_document
                        ? asset('storage/' . $record->bast_document)
                        : null)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->extraAttributes(['download' => '']),

                Tables\Columns\TextColumn::make('user.name')->label('User')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status_group')
                ->label('Status')
                ->options([
                    'on_going' => 'On Progress & Pending',
                    'rfu' => 'RFU (Selesai)',
                ])
                ->query(function ($query, $data) {
                    if (!empty($data['value'])) {
                        if ($data['value'] === 'on_going') {
                            $query->whereIn('status', ['in progress', 'pending']);
                        } elseif ($data['value'] === 'rfu') {
                            $query->where('status', 'like', '%RFU%');
                        }
                    }
                })
                ->indicateUsing(function ($data) {
                    if (!empty($data['value'])) {
                        if ($data['value'] === 'on_going') {
                            return 'Status: On Going';
                        } elseif ($data['value'] === 'rfu') {
                            return 'Status: RFU';
                        }
                    }
                    return null;
                })
                    
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->visible(fn (Activity $record): bool => auth()->user()->can('update activities')),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Download CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exporter(ActivityExporter::class),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                ->visible(fn () => auth()->user()->can('delete activities')),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
{
    $query = parent::getEloquentQuery();

    if (Auth::check()) {
        $user = Auth::user();
        Log::info('logged in', [$user]);
        if ($user->hasRole('DLH')) {
            Log::info('role DLH', [$user->roles]);
            if ($user->project_id) {
                return $query->where('project_id', $user->project_id);
            } else {
                // DLH tanpa project_id -> tidak bisa lihat data
                return $query->whereRaw('1 = 0');
            }
        }
        Log::info('role not DLH', [$user->roles]);

        // Admin dan role lain bisa lihat semua data
        return $query;
    }
    Log::info('not logged in', []);

    // Kalau tidak login, tidak bisa lihat data
    return $query->whereRaw('1 = 0');
}



public static function canCreate(): bool
{
    return auth()->user()?->can('create activities');
}

public static function canEdit($record): bool
{
    return auth()->user()?->can('update activities');
}

public static function canDelete($record): bool
{
    return auth()->user()?->can('delete activities');
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
