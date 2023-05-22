<?php

namespace Database\Seeders;

use Botble\Setting\Models\Setting as SettingModel;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        SettingModel::whereIn('key', ['media_random_hash'])->delete();

        SettingModel::insertOrIgnore([
            [
                'key' => 'media_random_hash',
                'value' => md5(time()),
            ],
        ]);
    }
}
