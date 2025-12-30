<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'order',
        'sector',
        'notification',
        'url',
    ];


    protected static function booted(): void
    {
        static::addGlobalScope('user_notifications', function (Builder $builder) {
            $user = Auth::user();
            $whereClausule = $user->role === 'Consultant' ? 'where' : 'orWhere';
            $builder->where(fn($q) => $q->where('sector', 'like', "%{$user->role}%")
                ->{$whereClausule}('user_id', $user->id));
        });
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }


}
