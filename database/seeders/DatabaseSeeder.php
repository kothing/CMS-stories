<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;

class DatabaseSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->activateAllPlugins();

        $this->call(UserSeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(PageSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(WidgetSeeder::class);
        $this->call(ContactSeeder::class);
        $this->call(ThemeOptionSeeder::class);
        $this->call(BlogSeeder::class);
        $this->call(GallerySeeder::class);
        $this->call(AdsSeeder::class);
        $this->call(SettingSeeder::class);

        $this->finished();
    }
}
