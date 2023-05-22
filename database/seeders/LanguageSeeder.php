<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Language\Models\Language;
use Botble\Language\Models\LanguageMeta;
use Botble\Setting\Models\Setting as SettingModel;

class LanguageSeeder extends BaseSeeder
{
    public function run(): void
    {
        $languages = [
            [
                'lang_name' => 'English',
                'lang_locale' => 'en',
                'lang_is_default' => true,
                'lang_code' => 'en_US',
                'lang_is_rtl' => false,
                'lang_flag' => 'us',
                'lang_order' => 0,
            ],
            [
                'lang_name' => 'Chinese',
                'lang_locale' => 'zh',
                'lang_is_default' => false,
                'lang_code' => 'zh_CN',
                'lang_is_rtl' => false,
                'lang_flag' => 'cn',
                'lang_order' => 0,
            ],
        ];

        Language::truncate();
        LanguageMeta::truncate();

        foreach ($languages as $item) {
            Language::create($item);
        }

        SettingModel::whereIn('key', [
            'language_hide_default',
            'language_switcher_display',
            'language_display',
            'language_hide_languages',
        ])
            ->delete();

        SettingModel::insertOrIgnore([
            [
                'key' => 'language_hide_default',
                'value' => '1',
            ],
            [
                'key' => 'language_switcher_display',
                'value' => 'list',
            ],
            [
                'key' => 'language_display',
                'value' => 'all',
            ],
            [
                'key' => 'language_hide_languages',
                'value' => '[]',
            ],
        ]);
    }
}
