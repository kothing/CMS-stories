<?php

namespace Botble\Theme\Providers;

use BaseHelper;
use Botble\Base\Supports\Helper;
use Composer\Autoload\ClassLoader;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Theme;

class ThemeManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $theme = Theme::getThemeName();
        if (! empty($theme)) {
            $this->app['translator']->addJsonPath(theme_path($theme . '/lang'));
        }
    }

    public function boot(): void
    {
        $theme = Theme::getThemeName();

        if (! empty($theme)) {
            $themePath = theme_path($theme);

            if (File::exists($themePath . '/theme.json')) {
                $content = BaseHelper::getFileData($themePath . '/theme.json');
                if (! empty($content)) {
                    if (Arr::has($content, 'namespace')) {
                        $loader = new ClassLoader();
                        $loader->setPsr4($content['namespace'], theme_path($theme . '/src'));
                        $loader->register();
                    }
                }
            }

            Helper::autoload(theme_path($theme . '/functions'));
        }
    }
}
