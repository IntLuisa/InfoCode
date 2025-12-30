<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class BooleanIntCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): bool
    {
        return (bool) $value;
    }

    public function set($model, string $key, $value, array $attributes): int
    {
        return $value ? 1 : 0;
    }
}
