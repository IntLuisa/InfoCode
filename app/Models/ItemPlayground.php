<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPlayground extends Model
{
    protected $table = 'playground_items';
    protected $fillable = [
        'id',
        'playground_id',
        'item_id',
        'quantity'
    ];
    public function items()
    {
        return $this->belongsToMany(Part::class, 'playground_items')->withPivot('id');
    }
    public function part()
    {
        return $this->belongsTo(Part::class, 'item_id');
    }

    public function itemsPlayground()
    {
        return $this->hasMany(ItemPlayground::class, 'playground_id');
    }
}
