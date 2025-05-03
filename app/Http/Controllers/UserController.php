<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
class UserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_type' => 'required|string|in:Admin,User,DLH', // Validasi role_type
            'project_id' => 'nullable|exists:projects,id', // Hanya untuk DLH
        ]);

        // Menentukan role_id sesuai dengan role_type
        $role = $validated['role_type'];

        // Membuat user baru dengan role_type yang sesuai
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_type' => $validated['role_type'], // Menyimpan role_type
            'project_id' => $role === 'DLH' ? $validated['project_id'] : null, // Hanya DLH yang memiliki project_id
        ]);

        // Memberikan role berdasarkan role_type
        $user->assignRole($role);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }
}
