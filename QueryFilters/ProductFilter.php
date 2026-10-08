<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Базовый фильтр. Каждый фильтр — отдельный класс.
 */
abstract class ProductFilter
{
    public function __construct(
        protected array $params = []
    ) {}

    abstract public function apply(Builder $query): Builder;
}
