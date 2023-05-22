<?php

namespace Botble\Base\Services;

use Botble\Setting\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class CleanDatabaseService
{
    public function getIgnoreTables(): array
    {
        return [
            'migrations',
            'pages',
            'users',
            'activations',
            'settings',
            'translations',
            'widgets',
            'menus',
            'menu_nodes',
        ];
    }

    public function execute(array $except = []): bool
    {
        $except = array_merge($except, $this->getIgnoreTables());

        Schema::disableForeignKeyConstraints();

        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        $tables = array_diff($tables, $except);

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        Setting::whereNotIn('key', [
            'theme',
            'activated_plugins',
            'licensed_to',
            'media_random_hash',
        ])->delete();

        File::cleanDirectory(Storage::disk()->path(''));

        return true;
    }
}
