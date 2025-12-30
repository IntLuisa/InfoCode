<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use SoftDeletes, Loggable;
    protected $appends = ['phone'];
    protected $fillable = [
        'name',
        'email',
        'phone',
        'job_position',
        'role',
    ];

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = preg_replace('/\D/', '', $value);
    }

    public function getPhoneAttribute()
    {
        $value = preg_replace('/\D/', '', $this->attributes['phone']);
        return preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '($1) $2-$3', preg_replace('/\D/', '', $value));
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
