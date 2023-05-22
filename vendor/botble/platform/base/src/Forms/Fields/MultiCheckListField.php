<?php

namespace Botble\Base\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class MultiCheckListField extends FormField
{
    protected function getTemplate(): string
    {
        return 'core/base::forms.fields.multi-check-list';
    }
}
