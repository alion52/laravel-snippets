<?php

namespace App\MoonShine\Fields;

use MoonShine\Fields\Text;

/**
 * Кастомное поле для MoonShine: название с автоматическим slug.
 */
class SlugField extends Text
{
    protected string $view = 'moonshine.fields.slug';

    protected string $fromField = 'title';

    public function fromField(string $field): static
    {
        $this->fromField = $field;
        return $this;
    }

    public function getFromField(): string
    {
        return $this->fromField;
    }
}
