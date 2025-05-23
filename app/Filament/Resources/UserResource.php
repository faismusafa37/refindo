<?php
namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Project;
use Spatie\Permission\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Access Management';
    protected static ?string $navigationLabel = 'User Management';

    public static function canAccess(): bool
    {
        return Auth::user()?->can('view users');
    }

    public static function canView($record): bool
    {
        return Auth::user()?->can('update users', $record);
    }
    

    public static function canEdit($record): bool
    {
        return Auth::user()?->can('update users', $record);
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->can('delete users', $record);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('password')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->maxLength(255)
                            ->label('Password'),
                    ]),

                Section::make('Roles & Project')
                    ->schema([
                        // Pilih Role
                        Select::make('role_id') // Menggunakan 'role_id' untuk relasi role
                            ->label('Assign Role(s)')
                            ->options(Role::pluck('name', 'id')) // Menampilkan nama role
                            ->reactive()
                            ->preload()
                            ->relationship('roles','name')
                            ->saveRelationshipsUsing(function ($record, $state) {
                                $role = Role::find($state);
                                $record->syncRoles([$role->name]);
                            })
                            ->required()
                            ->afterStateUpdated(fn (callable $set) => $set('project_id', null)), // Reset project_id jika role berubah

                        // Pilih Project, tampilkan jika role adalah DLH atau DLH Viewer
                        Select::make('project_id')
                            ->label('Project')
                            ->options(Project::pluck('name', 'id'))
                            ->searchable()
                            ->required(fn (Get $get) => in_array($get('role_id'), [
                                Role::where('name', 'DLH')->first()?->id,
                                Role::where('name', 'DLH Viewer')->first()?->id,
                            ])) // Required jika role DLH atau DLH Viewer
                            ->visible(fn (callable $get) => in_array($get('role_id'), [
                                Role::where('name', 'DLH')->first()?->id,
                                Role::where('name', 'DLH Viewer')->first()?->id,
                            ])), // Tampilkan jika role DLH atau DLH Viewer
                    ]), 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('first_role') // Mengakses relasi role
                    ->label('Roles')
                    ->badge()
                    ->sortable(),

                TextColumn::make('project.name') // Menampilkan nama project
                    ->label('Project')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
