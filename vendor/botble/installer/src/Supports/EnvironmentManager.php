<?php

namespace Botble\Installer\Supports;

use Exception;
use Illuminate\Http\Request;

class EnvironmentManager
{
    public function save(Request $request): string
    {
        $results = trans('packages/installer::installer.environment.success');

        $content = file_get_contents(base_path('.env.example'));

        $replacements = [
            'APP_NAME' => [
                'default' => '"Your App"',
                'value' => '"' . $request->input('app_name') . '"',
            ],
            'APP_URL' => [
                'default' => 'http:\/\/localhost',
                'value' => $request->input('app_url'),
            ],
            'DB_CONNECTION' => [
                'default' => 'mysql',
                'value' => $request->input('database_connection'),
            ],
            'DB_HOST' => [
                'default' => '127.0.0.1',
                'value' => $request->input('database_hostname'),
            ],
            'DB_PORT' => [
                'default' => '3306',
                'value' => $request->input('database_port'),
            ],
            'DB_DATABASE' => [
                'default' => '"laravel"',
                'value' => '"' . $request->input('database_name') . '"',
            ],
            'DB_USERNAME' => [
                'default' => '"root"',
                'value' => '"' . $request->input('database_username') . '"',
            ],
            'DB_PASSWORD' => [
                'default' => '"your_db_password"',
                'value' => '"' . $request->input('database_password') . '"',
            ],
        ];

        foreach ($replacements as $key => $replacement) {
            $content = preg_replace(
                '/^' . $key . '=' . $replacement['default'] . '/m',
                $key . '=' . $replacement['value'],
                $content
            );
        }

        try {
            file_put_contents(base_path('.env'), $content);
        } catch (Exception) {
            $results = trans('packages/installer::installer.environment.errors');
        }

        return $results;
    }
}
