<?php

namespace Botble\Language;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Models\Setting as SettingModel;
use Illuminate\Support\Facades\Schema;
use Setting;

class Plugin extends PluginOperationAbstract
{
    public static function activated()
    {
        $plugins = get_active_plugins();

        if (($key = array_search('language', $plugins)) !== false) {
            unset($plugins[$key]);
        }

        array_unshift($plugins, 'language');

        Setting::set('activated_plugins', json_encode($plugins))->save();
    }

    public static function remove()
    {
        Schema::dropIfExists('languages');
        Schema::dropIfExists('language_meta');

        SettingModel::query()
            ->whereIn('key', [
                'language_hide_default',
                'language_switcher_display',
                'language_display',
                'language_hide_languages',
            ])
            ->delete();
    }
}
