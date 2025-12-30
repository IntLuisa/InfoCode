<?php

namespace App\Filters;

use App\Filters\Filter;

class PlaygroundFilter extends Filter
{
    protected $safeParams = [
        'id' => ['eq'],
        'code' => ['eq'],
        'name' => ['lk'],
    ];
}
