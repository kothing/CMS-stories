<?php

use Botble\Base\Facades\DashboardMenuFacade;
use Botble\Base\Facades\PageTitleFacade;
use Botble\Base\Supports\DashboardMenu;
use Botble\Base\Supports\Editor;
use Botble\Base\Supports\PageTitle;
use Illuminate\Support\Arr;

if (! function_exists('language_flag')) {
    function language_flag(string $flag, ?string $name = null): string
    {
        return Html::image(asset(BASE_LANGUAGE_FLAG_PATH . $flag . '.svg'), $name, ['title' => $name, 'width' => 16]);
    }
}

if (! function_exists('render_editor')) {
    function render_editor(
        string $name,
        ?string $value = null,
        bool $withShortCode = false,
        array $attributes = []
    ): string {
        return (new Editor())->registerAssets()->render($name, $value, $withShortCode, $attributes);
    }
}

if (! function_exists('is_in_admin')) {
    function is_in_admin(bool $force = false): bool
    {
        $prefix = BaseHelper::getAdminPrefix();

        $segments = array_slice(request()->segments(), 0, count(explode('/', $prefix)));

        $isInAdmin = implode('/', $segments) === $prefix;

        return $force ? $isInAdmin : apply_filters(IS_IN_ADMIN_FILTER, $isInAdmin);
    }
}

if (! function_exists('page_title')) {
    function page_title(): PageTitle
    {
        return PageTitleFacade::getFacadeRoot();
    }
}

if (! function_exists('dashboard_menu')) {
    function dashboard_menu(): DashboardMenu
    {
        return DashboardMenuFacade::getFacadeRoot();
    }
}

if (! function_exists('get_cms_version')) {
    function get_cms_version(): string
    {
        $version = '...';

        try {
            $core = BaseHelper::getFileData(core_path('core.json'));

            return Arr::get($core, 'version', $version);
        } catch (Exception) {
            return $version;
        }
    }
}

if (! function_exists('get_core_version')) {
    function get_core_version(): string
    {
        return '6.4.0';
    }
}

if (! function_exists('platform_path')) {
    function platform_path(?string $path = null): string
    {
        return base_path('platform/' . $path);
    }
}

if (! function_exists('core_path')) {
    function core_path(?string $path = null): string
    {
        return platform_path('core/' . $path);
    }
}

if (! function_exists('package_path')) {
    function package_path(?string $path = null): string
    {
        return platform_path('packages/' . $path);
    }
}
