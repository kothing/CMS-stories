<?php

namespace Botble\Captcha;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Models\Setting;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Setting::query()
            ->whereIn('key', [
                'enable_captcha',
                'captcha_type',
                'captcha_hide_badge',
                'captcha_site_key',
                'captcha_secret',
            ])
            ->delete();
    }
}
