<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laragear\WebAuthn\WebAuthnAuthentication;
use Laragear\WebAuthn\Contracts\WebAuthnAuthenticatable;

class User extends Authenticatable implements WebAuthnAuthenticatable
{
    use HasApiTokens, Loggable, WebAuthnAuthentication;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'phone',
        'job_position',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = preg_replace('/\D/', '', $value);
    }

    public function getPhoneAttribute()
    {
        $value = preg_replace('/\D/', '', $this->attributes['phone']);
        return preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '($1) $2-$3', preg_replace('/\D/', '', $value));
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    public function assignRole($role)
    {
        $this->role = $role;
        $this->save();
    }
    public function responsibleEvents()
    {
        return $this->hasMany(CalendarEvent::class, 'responsible_id');
    }
    public function isManagerOrBoard()
    {
        return in_array($this->role, ['Manager', 'Board']);
    }

    public function calendarEvents()
    {
        return $this->hasMany(CalendarEvent::class, 'user_id');
    }

    public function assignedCalendarEvents()
    {
        return $this->hasMany(CalendarEvent::class, 'responsible_id');
    }
}
