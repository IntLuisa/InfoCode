<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'user_id',
        'model_id',
        'action',
        'model',
        'changes',
        'original',
    ];

    protected $appends = ['created_at'];

    public function getCreatedAtAttribute()
    {
        return date('d/m/Y à\s H:i:s', strtotime($this->attributes['created_at'] ?? 'now'));
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
