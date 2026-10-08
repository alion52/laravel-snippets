<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Pipeline фильтров.
 *
 * Применяет фильтры по цепочке. Каждый фильтр — отдельный класс.
 * Легко добавлять новые фильтры, не меняя контроллер.
 */
class FilterPipeline
{
    /** @var string[] */
    protected array $filters = [
        PriceFilter::class,
        CategoryFilter::class,
        StockFilter::class,
        BrandFilter::class,
    ];

    public function apply(Builder $query, array $params): Builder
    {
        foreach ($this->filters as $filterClass) {
            /** @var ProductFilter $filter */
            $filter = new $filterClass($params);
            $query = $filter->apply($query);
        }

        return $query;
    }

    public function add(string $filterClass): static
    {
        $this->filters[] = $filterClass;
        return $this;
    }
}
