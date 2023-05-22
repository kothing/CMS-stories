<?php

namespace Botble\Base\Forms\Fields;

use Illuminate\Support\Arr;
use Kris\LaravelFormBuilder\Fields\FormField;

class EditorField extends FormField
{
    protected function getTemplate(): string
    {
        return 'core/base::forms.fields.editor';
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true): string
    {
        $options['with-short-code'] = Arr::get($options, 'with-short-code', false);

        return parent::render($options, $showLabel, $showField, $showError);
    }
}
