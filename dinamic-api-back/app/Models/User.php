<?php

namespace App\Models;

use App\Notifications\ResetPasswordCustom;
use App\Notifications\VerifyEmailCustom;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at'
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
            'password' => 'hashed',
        ];
    }

    public function platformConnections()
    {
        return $this->hasMany(PlatformConnection::class);
    }

    public function databaseConnections()
    {
        return $this->hasMany(DatabaseConnection::class);
    }

    public function apiCallMappings()
    {
        return $this->hasMany(ApiCallMapping::class);
    }

    public function apiCallMappingFields()
    {
        return $this->hasManyThrough(
            ApiCallMappingField::class,
            ApiCallMapping::class
        );
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordCustom($token));
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailCustom());
    }

}
