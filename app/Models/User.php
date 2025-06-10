<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'phonenumber', // add this line
        'otp_code',          // ✅ add this
        'otp_expires_at',    // ✅ and this

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

    // All events created by this user
    public function events()
    {
        return $this->hasMany(Event::class);
    }


    // ✅ Helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOrganizer(): bool
    {
        return $this->role === 'organizer';
    }

    public function isAttendee(): bool
    {
        return $this->role === 'attendee';
    }
}
