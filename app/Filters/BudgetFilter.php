<?php

namespace App\Filters;

use App\Filters\Filter;

class BudgetFilter extends Filter
{
    protected $safeParams = [
        'id' => ['eq'],
        'status' => ['eq'],
    ];
}
