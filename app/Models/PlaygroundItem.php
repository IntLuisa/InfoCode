<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaygroundItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'playground_id',
        'item_id',
        'quantity',
    ];
}
