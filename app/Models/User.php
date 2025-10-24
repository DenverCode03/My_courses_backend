<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Resources\Enum\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Resources\UuidTrait;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, UuidTrait;


    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'role',
        'profile',
        'password',
        'verification_token',
    ];

    protected $casts = [
        'role' => UserRole::class
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

    public function apiTokens(): HasMany
    {
        return $this->hasMany(ApiToken::class);
    }

    public  function createToken(string $name, $expire_in = 15){
        $token = hash('sha256', Str::random(60));
        $apitoken = $this->apiTokens()->create([
            'token' => $token,
            'name' => $name,
            'expires_at' => now()->addDays($expire_in),
        ]);
        return $apitoken;
    }

    public function revokeToken(string $token){
        $this->apiTokens()->where('token', $token)->delete();
    }

    public function revokeAllTokens(){
        $this->apiTokens()->delete();
    }

    public function revokeExpiredTokens(){
        $this->apiTokens()->where('expires_at', '<', now())->delete();
    }

    public function revokeAllExpiredTokens(){
        $this->apiTokens()->where('expires_at', '<', now())->delete();
    }
}
