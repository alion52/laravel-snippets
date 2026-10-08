<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class PriceFilter extends ProductFilter
{
    public function apply(Builder $query): Builder
    {
        $min = $this->params['min_price'] ?? null;
        $max = $this->params['max_price'] ?? null;

        if ($min === null && $max === null) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($min, $max) {
            if ($min !== null) {
                $q->where('price', '>=', (float) $min);
            }
            if ($max !== null) {
                $q->where('price', '<=', (float) $max);
            }
        });
    }
}
