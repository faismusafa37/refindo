<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Permissions
        $permissions = [
            'view activities',        // View data aktivitas
            'create activities',      // Create aktivitas
            'update activities',      // Update aktivitas
            'delete activities',      // Delete aktivitas
            'view anggaran',          // View anggaran
            'create anggaran',        // Create anggaran
            'update anggaran',        // Update anggaran
            'delete anggaran',        // Delete anggaran
            'view stock opname',      // View stock opname
            'create stock opname',    // Create stock opname
            'update stock opname',    // Update stock opname
            'delete stock opname',    // Delete stock opname
            'view part dismantle',    // View part dismantle
            'create part dismantle',  // Create part dismantle
            'update part dismantle',  // Update part dismantle
            'delete part dismantle',  // Delete part dismantle
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
