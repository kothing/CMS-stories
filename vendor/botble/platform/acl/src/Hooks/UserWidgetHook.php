<?php

namespace Botble\ACL\Hooks;

use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Illuminate\Support\Collection;

class UserWidgetHook
{
    public static function addUserStatsWidget(array $widgets, Collection $widgetSettings): array
    {
        $users = app(UserInterface::class)->count();

        return (new DashboardWidgetInstance())
            ->setType('stats')
            ->setPermission('users.index')
            ->setTitle(trans('core/acl::users.users'))
            ->setKey('widget_total_users')
            ->setIcon('fas fa-users')
            ->setColor('#3598dc')
            ->setStatsTotal($users)
            ->setRoute(route('users.index'))
            ->init($widgets, $widgetSettings);
    }
}
