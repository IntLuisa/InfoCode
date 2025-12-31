<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'client_id',
        'status',
        'project_name',
        'service_type',
        'note',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
