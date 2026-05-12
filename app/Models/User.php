<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'protected_area_id',
        'is_active',
        'last_login_at',
        'login_count',
    ];

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
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'login_count' => 'integer',
            'protected_area_id' => 'integer',
        ];
    }

    /**
     * Get the attributes that should have default values.
     *
     * @return array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
        'login_count' => 0,
    ];

    public function protectedArea(): BelongsTo
    {
        return $this->belongsTo(ProtectedArea::class);
    }

    /**
     * Prefix @ for handle-style usernames only; skip for email-like values or stored @ handles.
     */
    public function usernameForDisplay(): string
    {
        $u = (string) ($this->username ?? '');
        if ($u === '') {
            return '';
        }
        if (str_starts_with($u, '@') || str_contains($u, '@')) {
            return $u;
        }

        return '@'.$u;
    }
}
