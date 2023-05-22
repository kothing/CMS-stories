<?php

namespace Botble\Slug;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SlugCompiler
{
    protected array $variables = [];

    public function __construct()
    {
        $now = Carbon::now();

        $this->variables = [
            '%%year%%' => [
                'label' => __('Current year'),
                'value' => $now->year,
            ],
            '%%month%%' => [
                'label' => __('Current month'),
                'value' => $now->month,
            ],
            '%%day%%' => [
                'label' => __('Current day'),
                'value' => $now->month,
            ],
        ];
    }

    public function getVariables(): array
    {
        return apply_filters(CMS_SLUG_VARIABLES, $this->variables);
    }

    public function compile(?string $prefix, Model|string|null $model = null): string
    {
        if (! $prefix) {
            return '';
        }

        foreach ($this->getVariables() as $key => $value) {
            $prefix = str_replace($key, $value['value'], $prefix);
        }

        return apply_filters('cms_slug_prefix', $prefix, $model);
    }
}
