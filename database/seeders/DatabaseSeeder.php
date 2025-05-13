<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        // User::factory(10)->create();
        $data = [
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('123123123'),
                'role_type' => 'admin',
            ],
            [
                'name' => 'DLH',
                'email' => 'dlh@dlh.com',
                'password' => Hash::make('123123123'),
                'role_type' => 'dlh',
            ],
            [
                'name' => 'User',
                'email' => 'user@user.com',
                'password' => Hash::make('123123123'),
                'role_type' => 'user',
            ],
        ];
        foreach($data as $user) {
            User::create($user);
        }
    }
}
