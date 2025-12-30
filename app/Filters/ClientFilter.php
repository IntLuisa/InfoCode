<?php

namespace App\Filters;

use App\Filters\Filter;

class ClientFilter extends Filter
{
    protected $safeParams = [
        'id' => ['eq'],
        'name' => ['lk'],
        'document' => ['lk'],
        'deleted_at' => ['eq'],
    ];
}
