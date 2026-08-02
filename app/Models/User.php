<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'dokter_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    /** Data master dokter yang tertaut ke akun ini (role dokter). */
    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDokter(): bool
    {
        return $this->role === 'dokter';
    }

    public function isApoteker(): bool
    {
        return $this->role === 'apoteker';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }
}
