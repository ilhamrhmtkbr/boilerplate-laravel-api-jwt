<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * User Eloquent Model — implements JWTSubject for php-open-source-saver/jwt-auth.
 *
 * Migration additions needed:
 *   $table->string('google_sub')->nullable()->unique();
 *   $table->string('avatar')->nullable();
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_sub',
        'avatar',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── JWTSubject Interface ───────────────────────────────────────

    /**
     * Get the identifier used in the JWT sub claim.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Custom claims to add to the JWT payload.
     * Add roles, permissions, tenant_id, etc. here.
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'email' => $this->email,
            'name'  => $this->name,
        ];
    }
}
