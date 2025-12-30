<?php

namespace App\Filters;

use App\Filters\Filter;

class UserFilter extends Filter
{
    protected $safeParams = [
        'id' => ['eq'],
        'name' => ['lk'],
        'email' => ['lk'],
        'created_at' => ['gte', 'lte'],
    ];
}
