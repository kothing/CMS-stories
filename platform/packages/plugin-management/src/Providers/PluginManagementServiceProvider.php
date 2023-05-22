<?php

namespace Botble\PluginManagement\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\PluginManagement\PluginManifest;
use Composer\Autoload\ClassLoader;
use Exception;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Psr\SimpleCache\InvalidArgumentException;

class PluginManagementServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function boot(): void
    {
        $this->setNamespace('packages/plugin-management')
            ->loadAndPublishConfigurations(['permissions', 'general'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadHelpers()
            ->publishAssets();

        $manifest = (new PluginManifest())->getManifest();

        $loader = new ClassLoader();

        foreach ($manifest['namespaces'] as $key => $namespace) {
            $loader->setPsr4($namespace, plugin_path($key . '/src'));
        }

        $loader->register();

        foreach ($manifest['providers'] as $provider) {
            if (! class_exists($provider)) {
                continue;
            }

            $this->app->register($provider);
        }

        $this->app->register(CommandServiceProvider::class);

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-core-plugins',
                    'priority' => 997,
                    'parent_id' => null,
                    'name' => 'core/base::layouts.plugins',
                    'icon' => 'fa fa-plug',
                    'url' => route('plugins.index'),
                    'permissions' => ['plugins.index'],
                ]);
        });

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });

        $this->app->register(EventServiceProvider::class);
    }
}
