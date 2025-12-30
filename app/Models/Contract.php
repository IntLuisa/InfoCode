<?php

namespace App\Models;

use App\Casts\BooleanIntCast;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contract extends Model
{
    use Loggable, HasFactory;

    protected $casts = [
        'approved' => BooleanIntCast::class,
    ];

    protected $fillable = [
        'service_id',
        'contract',
        'approved',
        'type',
    ];

    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }
}
