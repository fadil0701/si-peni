<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleName): bool
    {
        // Load roles if not already loaded
        if (!$this->relationLoaded('roles')) {
            $this->load('roles');
        }
        
        return $this->roles->contains('name', $roleName);
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleNames): bool
    {
        // Load roles if not already loaded
        if (!$this->relationLoaded('roles')) {
            $this->load('roles');
        }
        
        return $this->roles->whereIn('name', $roleNames)->isNotEmpty();
    }

    /**
     * Get the primary role (first role)
     */
    public function getPrimaryRoleAttribute()
    {
        return $this->roles()->first();
    }

    /**
     * Get the pegawai associated with this user
     */
    public function pegawai()
    {
        return $this->hasOne(MasterPegawai::class, 'user_id', 'id');
    }

    /**
     * Check if user has a specific permission through their roles
     */
    public function hasPermission(string $permissionName): bool
    {
        // Admin always has all permissions
        if ($this->hasRole('admin')) {
            return true;
        }

        // Load roles with permissions if not already loaded
        if (!$this->relationLoaded('roles')) {
            $this->load('roles.permissions');
        } else {
            foreach ($this->roles as $role) {
                if (!$role->relationLoaded('permissions')) {
                    $role->load('permissions');
                }
            }
        }

        // Check if any role has this permission
        foreach ($this->roles as $role) {
            // Check exact permission
            if ($role->permissions->contains('name', $permissionName)) {
                return true;
            }

            // Check wildcard permissions (e.g., 'inventory.*' matches 'inventory.data-stock.index')
            foreach ($role->permissions as $permission) {
                if (str_ends_with($permission->name, '.*')) {
                    $prefix = str_replace('.*', '', $permission->name);
                    if (str_starts_with($permissionName, $prefix . '.')) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
}
