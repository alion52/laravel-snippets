<?php

namespace App\Http\Controllers;

use App\Services\Geo\GeoRouteService;
use Illuminate\Http\Request;

class GeoController extends Controller
{
    public function __construct(
        private GeoRouteService $geo
    ) {}

    /**
     * /{slug}/ — область или город.
     */
    public function show(string $slug)
    {
        $context = $this->geo->resolve($slug);

        if (!$context) {
            abort(404);
        }

        $nurseries = $this->geo->queryFor($context)
            ->with(['city', 'region', 'plants'])
            ->paginate(12);

        return view('catalog.geo', [
            'context' => $context,
            'nurseries' => $nurseries,
            'breadcrumbs' => $this->breadcrumbs($context),
            'seo' => $this->seoFor($context),
        ]);
    }

    /**
     * /{region}/{plant}/ — растения в области.
     */
    public function showRegionPlant(string $region, string $plant)
    {
        $context = $this->geo->resolveCompound($region, $plant);

        if (!$context) {
            abort(404);
        }

        $nurseries = $this->geo->queryFor($context)
            ->with(['city', 'region'])
            ->paginate(12);

        return view('catalog.geo-plant', [
            'context' => $context,
            'nurseries' => $nurseries,
            'breadcrumbs' => $this->breadcrumbs($context),
            'seo' => $this->seoFor($context),
        ]);
    }

    /**
     * Хлебные крошки для страницы.
     */
    private function breadcrumbs(array $context): array
    {
        $crumbs = [
            ['title' => 'Главная', 'url' => url('/')],
            ['title' => 'Каталог', 'url' => route('catalog')],
        ];

        if (isset($context['region'])) {
            $crumbs[] = [
                'title' => $context['region']->name,
                'url' => $this->geo->urlFor($context['region']),
            ];
        }

        if (isset($context['city'])) {
            $crumbs[] = [
                'title' => $context['city']->name,
                'url' => $this->geo->urlFor($context['city']),
            ];
        }

        if (isset($context['plant'])) {
            $crumbs[] = [
                'title' => $context['plant']->name,
                'url' => $this->geo->urlFor($context['plant']),
            ];
        }

        return $crumbs;
    }

    /**
     * SEO-данные для страницы.
     */
    private function seoFor(array $context): array
    {
        if (isset($context['plant']) && isset($context['region'])) {
            $title = "Питомники растений в {$context['region']->name}: {$context['plant']->name}";
        } elseif (isset($context['city'])) {
            $title = "Питомники растений в {$context['city']->name}";
        } elseif (isset($context['region'])) {
            $title = "Питомники растений в {$context['region']->name}";
        } else {
            $title = 'Каталог питомников';
        }

        return [
            'title' => $title,
            'description' => "Каталог питомников растений. {$title}. Контакты, прайсы, отзывы.",
            'h1' => $title,
        ];
    }
}
