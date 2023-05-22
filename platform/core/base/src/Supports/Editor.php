<?php

namespace Botble\Base\Supports;

use Assets;
use BaseHelper;
use Illuminate\Support\Arr;

class Editor
{
    public function registerAssets(): self
    {
        Assets::addScriptsDirectly(
            config('core.base.general.editor.' . BaseHelper::getRichEditor() . '.js')
        )
            ->addScriptsDirectly('vendor/core/core/base/js/editor.js');

        return $this;
    }

    public function render(string $name, $value = null, bool $withShortcode = false, array $attributes = []): string
    {
        $attributes['class'] = Arr::get($attributes, 'class', '') . ' editor-' . BaseHelper::getRichEditor();

        $attributes['id'] = Arr::has($attributes, 'id') ? $attributes['id'] : $name;
        $attributes['with-short-code'] = $withShortcode;
        $attributes['rows'] = Arr::get($attributes, 'rows', 4);

        return view('core/base::forms.partials.editor', compact('name', 'value', 'attributes'))
            ->render();
    }
}
