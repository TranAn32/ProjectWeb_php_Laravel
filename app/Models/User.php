<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $timestamps = true;

    protected $fillable = [
        // Note: DB column is 'userName', we expose it as 'username' attribute via accessor/mutator below
        'username',
        'email',
        'password',
        'phone_number',
        'address',
        'gender',
        'role',
        'status',
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
            // 'password' => 'hashed', // optional: your data may be pre-hashed or plain in seed
        ];
    }

    /**
     * Map the virtual 'username' attribute to the actual DB column 'userName'.
     */
    protected function username(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['userName'] ?? null,
            set: fn($value) => ['userName' => $value],
        );
    }

    // Back-compat accessors
    public function getNameAttribute()
    {
        return $this->username;
    }
}
