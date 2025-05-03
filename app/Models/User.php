<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
/**
 * @method bool hasRole(string|array|\Spatie\Permission\Models\Role $roles, ?string $guard = null)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'password', 'role_id', 'project_id', 'role_type'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $guard_name = 'web';

    protected $appends = ['first_role', 'role_id'];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

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


    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
