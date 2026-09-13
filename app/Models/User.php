<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Wilayah yang ditugaskan ke pengguna.
     * Satu pengguna dapat menangani lebih dari satu wilayah.
     */
    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class)
            ->withPivot([
                'active',
                'assigned_by',
                'assigned_at',
            ])
            ->withTimestamps();
    }

    /**
     * ID wilayah aktif pengguna.
     * Superadmin tidak memakai daftar ini karena memiliki akses global.
     */
    public function activeAreaIds(): Collection
    {
        return $this->areas()
            ->wherePivot('active', true)
            ->where('areas.active', true)
            ->pluck('areas.id');
    }

    /**
     * Role superadmin saat ini memakai kode super_admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
}
