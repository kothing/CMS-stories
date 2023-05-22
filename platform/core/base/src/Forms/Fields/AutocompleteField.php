<?php

namespace Botble\Base\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\SelectType;

class AutocompleteField extends SelectType
{
    protected function getTemplate(): string
    {
        return 'core/base::forms.fields.autocomplete';
    }
}
