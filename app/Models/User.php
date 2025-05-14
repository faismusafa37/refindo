<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;

/**
 * @method bool hasRole(string|array|\Spatie\Permission\Models\Role $roles, ?string $guard = null)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = ['name', 'email', 'password', 'role_id', 'project_id', 'role_type'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $guard_name = 'web';

    protected $appends = ['first_role', 'role_id'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ====================
    // GETTER UNTUK ROLE
    // ====================
    public function getRoleIdAttribute()
    {
        if (!empty($this->roles) && !empty($this->roles[0])) {
            return $this->roles[0]->id;
        }
        return null;
    }

    public function getFirstRoleAttribute()
    {
        if (!empty($this->roles) && !empty($this->roles[0])) {
            return $this->roles[0]->name;
        }
        return '';
    }

    // ====================
    // RELASI PROJECT
    // ====================
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // ====================
    // OVERRIDE assignRole AGAR MENGISI role_type
    // ====================
    

    // ====================
    // OVERRIDE syncRoles AGAR MENGISI role_type
    // ====================
}
