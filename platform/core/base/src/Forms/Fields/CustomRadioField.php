<?php

namespace Botble\Base\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class CustomRadioField extends FormField
{
    protected function getTemplate(): string
    {
        return 'core/base::forms.fields.custom-radio';
    }
}
