<?php

namespace Botble\PluginManagement\Commands;

use BaseHelper;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:plugin:list', 'Show all plugins information')]
class PluginListCommand extends Command
{
    public function handle(): int
    {
        $header = [
            'Name',
            'Alias',
            'Version',
            'Provider',
            'Status',
            'Author',
        ];
        $result = [];

        $plugins = BaseHelper::scanFolder(plugin_path());
        if (! empty($plugins)) {
            $installed = get_active_plugins();
            foreach ($plugins as $plugin) {
                $configFile = plugin_path($plugin . '/plugin.json');
                if (! File::exists($configFile)) {
                    continue;
                }

                $content = BaseHelper::getFileData($configFile);
                if (! empty($content)) {
                    $result[] = [
                        Arr::get($content, 'name'),
                        $plugin,
                        Arr::get($content, 'version'),
                        Arr::get($content, 'provider'),
                        in_array($plugin, $installed) ? '✓ active' : '✘ inactive',
                        Arr::get($content, 'author'),
                    ];
                }
            }
        }

        $this->table($header, $result);

        return self::SUCCESS;
    }
}
