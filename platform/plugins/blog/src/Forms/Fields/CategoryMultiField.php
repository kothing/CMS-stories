<?php

namespace Botble\Blog\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class CategoryMultiField extends FormField
{
    protected function getTemplate(): string
    {
        return 'plugins/blog::categories.categories-multi';
    }
}
