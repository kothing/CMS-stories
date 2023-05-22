<?php

namespace Botble\Base\Traits;

use Botble\Base\Supports\Helper;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;

/**
 * @mixin ServiceProvider
 */
trait LoadAndPublishDataTrait
{
    protected ?string $namespace = null;

    public function setNamespace(string $namespace): self
    {
        $this->namespace = ltrim(rtrim($namespace, '/'), '/');

        $this->app['config']->set(['core.base.general.plugin_namespaces.' . File::basename($this->getPath()) => $namespace]);

        return $this;
    }

    protected function getPath(string $path = null): string
    {
        $reflection = new ReflectionClass($this);

        $modulePath = str_replace('/src/Providers', '', File::dirname($reflection->getFilename()));

        if (! Str::contains($modulePath, base_path('platform/plugins'))) {
            $modulePath = base_path('platform/' . $this->getDashedNamespace());
        }

        return $modulePath . ($path ? '/' . ltrim($path, '/') : '');
    }

    public function loadAndPublishConfigurations(array|string $fileNames): self
    {
        if (! is_array($fileNames)) {
            $fileNames = [$fileNames];
        }

        foreach ($fileNames as $fileName) {
            $this->mergeConfigFrom($this->getConfigFilePath($fileName), $this->getDotedNamespace() . '.' . $fileName);

            if ($this->app->runningInConsole()) {
                $this->publishes([
                    $this->getConfigFilePath($fileName) => config_path($this->getDashedNamespace() . '/' . $fileName . '.php'),
                ], 'cms-config');
            }
        }

        return $this;
    }

    protected function getConfigFilePath(string $file): string
    {
        return $this->getPath('config/' . $file . '.php');
    }

    protected function getDashedNamespace(): string
    {
        return str_replace('.', '/', $this->namespace);
    }

    protected function getDotedNamespace(): string
    {
        return str_replace('/', '.', $this->namespace);
    }

    public function loadRoutes(array|string $fileNames = ['web']): self
    {
        if (! is_array($fileNames)) {
            $fileNames = [$fileNames];
        }

        foreach ($fileNames as $fileName) {
            $this->loadRoutesFrom($this->getRouteFilePath($fileName));
        }

        return $this;
    }

    protected function getRouteFilePath(string $file): string
    {
        return $this->getPath('routes/' . $file . '.php');
    }

    public function loadAndPublishViews(): self
    {
        $this->loadViewsFrom($this->getViewsPath(), $this->getDashedNamespace());
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [$this->getViewsPath() => resource_path('views/vendor/' . $this->getDashedNamespace())],
                'cms-views'
            );
        }

        return $this;
    }

    protected function getViewsPath(): string
    {
        return $this->getPath('/resources/views');
    }

    public function loadAndPublishTranslations(): self
    {
        $this->loadTranslationsFrom($this->getTranslationsPath(), $this->getDashedNamespace());
        $this->publishes(
            [$this->getTranslationsPath() => lang_path('vendor/' . $this->getDashedNamespace())],
            'cms-lang'
        );

        return $this;
    }

    protected function getTranslationsPath(): string
    {
        return $this->getPath('/resources/lang');
    }

    public function loadMigrations(): self
    {
        $this->loadMigrationsFrom($this->getMigrationsPath());

        return $this;
    }

    protected function getMigrationsPath(): string
    {
        return $this->getPath('/database/migrations');
    }

    public function publishAssets(string $path = null): self
    {
        if ($this->app->runningInConsole()) {
            if (empty($path)) {
                $path = 'vendor/core/' . $this->getDashedNamespace();
            }

            $this->publishes([$this->getAssetsPath() => public_path($path)], 'cms-public');
        }

        return $this;
    }

    protected function getAssetsPath(): string
    {
        return $this->getPath('public');
    }

    public function loadHelpers(): self
    {
        Helper::autoload($this->getPath('/helpers'));

        return $this;
    }
}
