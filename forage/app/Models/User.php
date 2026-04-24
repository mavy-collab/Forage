<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property \Carbon\Carbon $created_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    // 'role' exclu du fillable volontairement — doit être assigné explicitement via setRole()
    public function setRole(string $role): void
    {
        $this->forceFill(['role' => $role])->save();
    }

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    public function depenses()
    {
        return $this->hasMany(Depense::class);
    }

    public function journalActivites()
    {
        return $this->hasMany(JournalActivite::class);
    }
}
