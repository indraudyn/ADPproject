<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Gunakan tabel 'User' (kapital) dari Prisma backend.
     */
    protected $table = 'User';

    /**
     * Prisma menggunakan 'createdAt' (camelCase).
     */
    const CREATED_AT = 'createdAt';

    /**
     * Tabel User dari Prisma tidak memiliki kolom updated_at.
     */
    const UPDATED_AT = null;

    /**
     * Nonaktifkan remember_token karena tabel Prisma tidak memiliki kolom ini.
     */
    protected $rememberTokenName = '';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
