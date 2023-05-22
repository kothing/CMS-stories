<?php

namespace Botble\Setting\Supports;

use Illuminate\Support\Manager;

class SettingsManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return config('core.setting.general.driver');
    }

    public function createJsonDriver(): JsonSettingStore
    {
        return new JsonSettingStore(app('files'));
    }

    public function createDatabaseDriver(): DatabaseSettingStore
    {
        return new DatabaseSettingStore();
    }
}
