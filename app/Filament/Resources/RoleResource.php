<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationLabel = 'Roles & Permissions';
    protected static ?string $navigationGroup = 'Access Management';
    protected static ?string $modelLabel = 'Role';

    public static function canAccess(): bool
    {
        return Auth::user()?->can('view roles');
    }

    public static function canView($record): bool
    {
        return Auth::user()?->can('update roles', $record);
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->can('create roles');
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->can('update roles', $record);
    }

    public static function form(Form $form): Form
    {
        // Group permissions by module
        $permissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);
            return count($parts) > 1 ? $parts[1] : 'other';
        });

        $permissionSections = [];
        foreach ($permissions as $module => $modulePermissions) {
            $permissionSections[] = Section::make(ucfirst($module))
                ->schema([
                    Forms\Components\CheckboxList::make('permissions')
                        ->label('')
                        ->relationship('permissions', 'name')
                        ->options($modulePermissions->pluck('name', 'id'))
                        ->searchable()
                        ->bulkToggleable()
                        ->gridDirection('row')
                        ->columns(2)
                ]);
        }

        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Role Name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                    ]),
                
                Card::make()
                    ->schema([
                        Section::make('Permissions')
                            ->schema($permissionSections)
                    ]),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Role Name')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissions Count'),
                    
                TextColumn::make('permissions.name')
                    ->label('Permissions')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (count($state) <= 3) {
                            return null;
                        }
                        return implode(', ', $state);
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        if (in_array($record->name, ['admin', 'user', 'dlh'])) {
                            throw new \Exception('System roles cannot be deleted');
                        }
                    }),
                    
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if (in_array($record->name, ['admin', 'user', 'dlh'])) {
                                    throw new \Exception('System roles cannot be deleted');
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // No relations needed as we've combined everything in one resource
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('admin');
    }
}