<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use App\Utils\Number;

class NumberStringCast implements CastsAttributes
{
    /**
     * Summary of get
     * @param mixed $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function get($model, string $key, $value, array $attributes): string
    {
        return Number::toString($value);
    }

    /**
     * Summary of set
     * @param mixed $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return float
     */

    public function set($model, string $key, $value, array $attributes): float
    {
        return Number::toFloat($value);
    }
}
