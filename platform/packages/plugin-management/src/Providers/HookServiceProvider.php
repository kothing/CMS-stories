<?php

namespace Botble\PluginManagement\Providers;

use BaseHelper;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'addStatsWidgets'], 15, 2);
    }

    public function addStatsWidgets(array $widgets, Collection $widgetSettings): array
    {
        $plugins = count(BaseHelper::scanFolder(plugin_path()));

        return (new DashboardWidgetInstance())
            ->setType('stats')
            ->setPermission('plugins.index')
            ->setTitle(trans('packages/plugin-management::plugin.plugins'))
            ->setKey('widget_total_plugins')
            ->setIcon('fa fa-plug')
            ->setColor('#8e44ad')
            ->setStatsTotal($plugins)
            ->setRoute(route('plugins.index'))
            ->init($widgets, $widgetSettings);
    }
}
