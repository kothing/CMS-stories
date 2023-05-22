<?php

namespace Botble\Captcha\Providers;

use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(BASE_FILTER_AFTER_SETTING_CONTENT, [$this, 'addSettings'], 299);

        add_filter(THEME_FRONT_HEADER, [$this, 'addHeaderMeta'], 299);
    }

    public function addSettings(?string $data = null): string
    {
        return $data . view('plugins/captcha::setting')->render();
    }

    public function addHeaderMeta(?string $html): string
    {
        return $html . view('plugins/captcha::header-meta')->render();
    }
}
