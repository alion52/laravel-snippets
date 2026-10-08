<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class CategoryFilter extends ProductFilter
{
    public function apply(Builder $query): Builder
    {
        $categoryId = $this->params['category_id'] ?? null;

        if (!$categoryId) {
            return $query;
        }

        return $query->whereHas('categories', function (Builder $q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }
}
