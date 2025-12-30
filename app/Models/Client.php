<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;



class Client extends Model
{
    use SoftDeletes, Loggable, HasFactory;
    protected $appends = ['document', 'zip_code', 'phone'];
    protected $fillable = [
        'name',
        'document',
        'email',
        'phone',
        'address',
        'number',
        'complement',
        'district',
        'state',
        'city',
        'zip_code',
        'origin',
        'stateRegistration',
        'codename',
    ];

    public function setZipCodeAttribute($value)
    {
        $this->attributes['zip_code'] = preg_replace('/\D/', '', $value);
    }

    public function getZipCodeAttribute()
    {
        $value = preg_replace('/\D/', '', $this->attributes['zip_code']);
        return preg_replace('/(\d{2})(\d{3})(\d{3})/', '$1.$2-$3', preg_replace('/\D/', '', $value));
    }

    public function setDocumentAttribute($value)
    {
        $this->attributes['document'] = empty($value) ? null : preg_replace('/\D/', '', $value);
    }

    public function getDocumentAttribute()
    {
        $value = preg_replace('/\D/', '', $this->attributes['document']);

        if (strlen($value) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $value);
        } elseif (strlen($value) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $value);
        }

        return $value;
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

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }
}
