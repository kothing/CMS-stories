<?php

namespace Botble\Newsletter;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Models\Setting;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('newsletters');

        Setting::query()
            ->whereIn('key', [
                'newsletter_mailchimp_api_key',
                'newsletter_mailchimp_list_id',
                'newsletter_sendgrid_api_key',
                'newsletter_sendgrid_list_id',
                'enable_newsletter_contacts_list_api',
            ])
            ->delete();
    }
}
