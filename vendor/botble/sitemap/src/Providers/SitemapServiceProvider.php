<?php

namespace Botble\Sitemap\Providers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Sitemap\Sitemap;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class SitemapServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    protected bool $defer = true;

    public function boot(): void
    {
        $this->setNamespace('packages/sitemap')
            ->loadAndPublishConfigurations(['config'])
            ->loadAndPublishViews()
            ->publishAssets();

        Event::listen(CreatedContentEvent::class, function () {
            cache()->forget('cache_site_map_key');
        });

        Event::listen(UpdatedContentEvent::class, function () {
            cache()->forget('cache_site_map_key');
        });

        Event::listen(DeletedContentEvent::class, function () {
            cache()->forget('cache_site_map_key');
        });
    }

    public function register(): void
    {
        $this->app->bind('sitemap', function ($app) {
            $config = config('packages.sitemap.config');

            return new Sitemap(
                $config,
                $app['Illuminate\Cache\Repository'],
                $app['config'],
                $app['files'],
                $app['Illuminate\Contracts\Routing\ResponseFactory'],
                $app['view']
            );
        });

        $this->app->alias('sitemap', Sitemap::class);
    }

    public function provides(): array
    {
        return ['sitemap', Sitemap::class];
    }
}
