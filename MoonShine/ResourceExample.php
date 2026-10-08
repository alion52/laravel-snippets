<?php

namespace App\MoonShine\Resources;

use App\Models\Nursery;
use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\HasMany;
use MoonShine\Fields\ID;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;

class NurseryResource extends ModelResource
{
    protected string $model = Nursery::class;

    protected string $title = 'Питомники';

    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name')->required(),
            Text::make('Slug', 'slug')->required(),
            BelongsTo::make('Город', 'city', 'name')
                ->searchable()
                ->required(),
            BelongsTo::make('Область', 'region', 'name')
                ->searchable()
                ->required(),
            HasMany::make('Растения', 'plants', 'name'),
        ];
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:nurseries,slug',
            'city_id' => 'required|exists:cities,id',
            'region_id' => 'required|exists:regions,id',
        ];
    }
}
