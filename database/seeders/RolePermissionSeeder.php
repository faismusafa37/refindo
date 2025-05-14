<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        Permission::whereNotNull('name')->delete();
        // Permissions
        $permissions = [
            // Activity Permissions
            'view activities',
            'create activities',
            'update activities',
            'delete activities',
        
            // Anggaran Permissions
            'view anggaran',
            'create anggaran',
            'update anggaran',
            'delete anggaran',
        
            // Stock Opname Permissions
            'view stock opname',
            'create stock opname',
            'update stock opname',
            'delete stock opname',
        
            // Part Dismantle Permissions
            'view part dismantle',
            'create part dismantle',
            'update part dismantle',
            'delete part dismantle',
        
            // Role & User Management Permissions
            'view roles',
            'create roles',
            'update roles',
            
        
            'view users',
            'create users',
            'update users',
            'delete users',

            'view budget absorption',
        ];
        

        // Create permissions if they do not exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user = Role::firstOrCreate(['name' => 'user']);
        $dlh = Role::firstOrCreate(['name' => 'dlh']);
    
        // Admin memiliki semua permission
        $admin->syncPermissions($permissions);
    
        // User hanya bisa create dan update, tanpa delete
        $user->syncPermissions([
            'create activities', 
            'update activities',
            'view activities',
        ]);
    
        // DLH hanya bisa melihat aktivitas
        $dlh->syncPermissions([
            'view activities',
        ]);
    }
}
