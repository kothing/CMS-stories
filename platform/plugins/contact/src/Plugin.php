<?php

namespace Botble\Contact;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Models\Setting;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('contact_replies');
        Schema::dropIfExists('contacts');

        Setting::query()
            ->whereIn('key', [
                'blacklist_keywords',
                'blacklist_email_domains',
                'enable_math_captcha_for_contact_form',
            ])
            ->delete();
    }
}
