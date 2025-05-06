<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            
        ];
    }

    /**
     * Saat data diisi ke form (untuk pre-fill)
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $role = Role::find($data['role_id'] ?? null);
        
        if ($role) {
            // Simpan role_name agar disinkronkan nanti
            $this->roleNameToSync = $role->name;
        }
    
        return $data;
    }
    
    protected function afterSave(): void
    {
        if (isset($this->roleNameToSync)) {
            $this->record->syncRoles([$this->roleNameToSync]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
