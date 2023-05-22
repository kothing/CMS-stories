<?php

use Botble\Setting\Facades\SettingFacade;
use Botble\Setting\Supports\SettingStore;
use Illuminate\Support\Collection;

if (! function_exists('setting')) {
    function setting(?string $key = null, $default = null)
    {
        if (! empty($key)) {
            try {
                return app(SettingStore::class)->get($key, $default);
            } catch (Throwable) {
                return $default;
            }
        }

        return SettingFacade::getFacadeRoot();
    }
}

if (! function_exists('get_admin_email')) {
    function get_admin_email(): Collection
    {
        $email = setting('admin_email');

        if (! $email) {
            return collect();
        }

        $email = is_array($email) ? $email : (array)json_decode($email, true);

        return collect(array_filter($email));
    }
}

if (! function_exists('get_setting_email_template_content')) {
    function get_setting_email_template_content(string $type, string $module, string $templateKey): string
    {
        $defaultPath = platform_path($type . '/' . $module . '/resources/email-templates/' . $templateKey . '.tpl');
        $storagePath = get_setting_email_template_path($module, $templateKey);

        if ($storagePath != null && File::exists($storagePath)) {
            return BaseHelper::getFileData($storagePath, false);
        }

        return File::exists($defaultPath) ? BaseHelper::getFileData($defaultPath, false) : '';
    }
}

if (! function_exists('get_setting_email_template_path')) {
    function get_setting_email_template_path(string $module, string $templateKey): string
    {
        return storage_path('app/email-templates/' . $module . '/' . $templateKey . '.tpl');
    }
}

if (! function_exists('get_setting_email_subject_key')) {
    function get_setting_email_subject_key(string $type, string $module, string $templateKey): string
    {
        return $type . '_' . $module . '_' . $templateKey . '_subject';
    }
}

if (! function_exists('get_setting_email_subject')) {
    function get_setting_email_subject(string $type, string $module, string $templateKey): string
    {
        return setting(
            get_setting_email_subject_key($type, $module, $templateKey),
            trans(
                config(
                    $type . '.' . $module . '.email.templates.' . $templateKey . '.subject',
                    ''
                )
            )
        );
    }
}

if (! function_exists('get_setting_email_status_key')) {
    function get_setting_email_status_key(string $type, string $module, string $templateKey): string
    {
        return $type . '_' . $module . '_' . $templateKey . '_' . 'status';
    }
}

if (! function_exists('get_setting_email_status')) {
    function get_setting_email_status(string $type, string $module, string $templateKey): string
    {
        $default = config($type . '.' . $module . '.email.templates.' . $templateKey . '.enabled', true);

        return setting(get_setting_email_status_key($type, $module, $templateKey), $default);
    }
}
