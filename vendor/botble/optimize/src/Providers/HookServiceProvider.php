<?php

namespace Botble\Optimize\Providers;

use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(BASE_FILTER_AFTER_SETTING_CONTENT, [$this, 'addSetting'], 27);
    }

    public function addSetting(?string $data = null): string
    {
        return $data . view('packages/optimize::setting')->render();
    }
}
