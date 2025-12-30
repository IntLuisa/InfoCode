<?php

namespace App\Filters;

use App\Filters\Filter;

class PartFilter extends Filter
{
    protected $safeParams = [
        'id' => ['eq'],
        'name' => ['lk'],
        'document' => ['lk'],
    ];
}
