<?php

namespace App\Models;

use App\Traits\Loggable;
use App\Utils\Number;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Part extends Model
{
    use HasFactory, SoftDeletes, Loggable;
    protected $table = 'items';
    protected $fillable = [
        'code',
        'name',
        'picture',
        'amount',
        'amountFabric',
        'ipiPercentage',
        'kits',
    ];

    protected $dates = ['deleted_at'];

    public function getAmountAttribute()
    {
        return is_numeric($this->getAttributes()['amount']) ? $this->getAttributes()['amount'] : 0;
    }


    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = Number::toFloat((string) ($value ?? '0'));
    }
    public function getAmountFabricAttribute()
    {
        return is_numeric($this->getAttributes()['amountFabric']) ? $this->getAttributes()['amountFabric'] : 0;
    }


    public function setAmountFabricAttribute($value)
    {
        $this->attributes['amountFabric'] = Number::toFloat((string) ($value ?? '0'));
    }

    public function playground()
    {
        return $this->belongsToMany(Playground::class, 'playground_items', 'item_id', 'playground_id');
    }

}
