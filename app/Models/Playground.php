<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Part;
use App\Models\ItemPlayground;
use App\Traits\Loggable;
use App\Utils\Number;

class Playground extends Model
{
    use HasFactory, SoftDeletes, Loggable;

    protected $appends = ['discount', 'thumb_url'];

    protected $casts = [
        'discount_factory' => 'float',
    ];
    protected $fillable = [
        'code',
        'name',
        'picture',
        'discount',
        'discount_factory',
    ];

    public function setDiscountAttribute($value)
    {
        $this->attributes['discount'] = Number::toFloat((string) $value);
    }

    public function getDiscountAttribute()
    {
        return $this->attributes['discount'];
    }

    public function items()
    {
        return $this->belongsToMany(Part::class, 'playground_items', 'playground_id', 'item_id')
            ->withPivot('id', 'quantity')
            ->withTimestamps()
            ->withTrashed()
            ->orderBy('items.name');
    }


    public function parts()
    {
        return $this->hasManyThrough(
            Part::class,
            ItemPlayground::class,
            'playground_id',
            'id',
            'id',
            'item_id'
        )->withTrashed();
    }
    public function getAmountAttribute()
    {
        return is_numeric($this->getAttributes()['discount']) ? $this->getAttributes()['discount'] : 0;
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['discount'] = Number::toFloat((string) ($value ?? '0'));
    }


    public function getParkAmountAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->part->price;
        });
    }

}
