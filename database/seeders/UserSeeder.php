<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Project; // Pastikan model Project sudah ada
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Membuat Role
        $adminRole = Role::create(['name' => 'Admin']);
        $userRole = Role::create(['name' => 'User']);
        $dlhRole = Role::create(['name' => 'DLH']);

        // Membuat Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_type' => 'Admin',
            'role_id' => $adminRole->id,
            'project_id' => null,
        ])->assignRole('Admin');

        // Membuat User Biasa
        User::create([
            'name' => 'Biasa User',
            'email' => 'biasa@example.com',
            'password' => bcrypt('password'),
            'role_type' => 'User',
            'role_id' => $userRole->id,
            'project_id' => null,
        ])->assignRole('User');

        // Ambil project yang ada dari tabel Project
        $project1 = Project::find(1); // Project dengan ID 1
        $project2 = Project::find(2); // Project dengan ID 2
        $project3 = Project::find(3); // Project dengan ID 3
        $project4 = Project::find(4); // Project dengan ID 4

        // Membuat User DLH dan mengaitkan dengan project yang sesuai
        User::create([
            'name' => 'DLH User 1',
            'email' => 'dlh1@example.com',
            'password' => bcrypt('password'),
            'role_type' => 'DLH',
            'role_id' => $dlhRole->id,
            'project_id' => $project1->id,  // Menetapkan project_id ke project 1
        ])->assignRole('DLH');

        User::create([
            'name' => 'DLH User 2',
            'email' => 'dlh2@example.com',
            'password' => bcrypt('password'),
            'role_type' => 'DLH',
            'role_id' => $dlhRole->id,
            'project_id' => $project2->id,  // Menetapkan project_id ke project 2
        ])->assignRole('DLH');

        User::create([
            'name' => 'DLH User 3',
            'email' => 'dlh3@example.com',
            'password' => bcrypt('password'),
            'role_type' => 'DLH',
            'role_id' => $dlhRole->id,
            'project_id' => $project3->id,  // Menetapkan project_id ke project 3
        ])->assignRole('DLH');

        User::create([
            'name' => 'DLH User 4',
            'email' => 'dlh4@example.com',
            'password' => bcrypt('password'),
            'role_type' => 'DLH',
            'role_id' => $dlhRole->id,
            'project_id' => $project4->id,  // Menetapkan project_id ke project 4
        ])->assignRole('DLH');
    }
}
