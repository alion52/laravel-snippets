<?php

namespace App\Services\Geo;

use App\Models\City;
use App\Models\Plant;
use App\Models\Region;
use Illuminate\Database\Eloquent\Builder;

/**
 * Формирование и разбор ЧПУ для геокаталога.
 *
 * Поддерживаемые URL:
 *   /{region}/                — питомники в области
 *   /{region}/{plant}/        — растения в области
 *   /{city}/                  — питомники в городе
 */
class GeoRouteService
{
    /**
     * Разбор URL и возврат контекста.
     */
    public function resolve(string $slug): ?array
    {
        // 1. Город
        $city = City::query()->where('slug', $slug)->first();
        if ($city) {
            return [
                'type' => 'city',
                'city' => $city,
                'region' => $city->region,
            ];
        }

        // 2. Область
        $region = Region::query()->where('slug', $slug)->first();
        if ($region) {
            return [
                'type' => 'region',
                'region' => $region,
            ];
        }

        return null;
    }

    /**
     * Разбор составного URL: /{region}/{plant}/
     */
    public function resolveCompound(string $regionSlug, string $plantSlug): ?array
    {
        $region = Region::query()->where('slug', $regionSlug)->first();
        if (!$region) {
            return null;
        }

        $plant = Plant::query()->where('slug', $plantSlug)->first();
        if (!$plant) {
            return null;
        }

        return [
            'type' => 'region_plant',
            'region' => $region,
            'plant' => $plant,
        ];
    }

    /**
     * Генерация URL для сущности.
     */
    public function urlFor($entity): string
    {
        return match (true) {
            $entity instanceof City => url("/{$entity->slug}/"),
            $entity instanceof Region => url("/{$entity->slug}/"),
            $entity instanceof Plant => url("/{$entity->slug}/"),
            default => url('/'),
        };
    }

    /**
     * Базовый запрос питомников с фильтром по региону или городу.
     */
    public function queryFor(array $context): Builder
    {
        $query = \App\Models\Nursery::query()->where('is_active', true);

        if (isset($context['city'])) {
            $query->where('city_id', $context['city']->id);
        } elseif (isset($context['region'])) {
            $query->where('region_id', $context['region']->id);
        }

        if (isset($context['plant'])) {
            $query->whereHas('plants', function ($q) use ($context) {
                $q->where('plants.id', $context['plant']->id);
            });
        }

        return $query;
    }
}
