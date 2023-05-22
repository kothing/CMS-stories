<?php

namespace Botble\PluginManagement\Commands;

use BaseHelper;
use Botble\PluginManagement\Services\PluginService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:plugin:remove:all', 'Remove all plugins in /plugins directory')]
class PluginRemoveAllCommand extends Command
{
    public function handle(PluginService $pluginService): int
    {
        if (! $this->components->confirm('Are you sure you want to remove ALL plugins?', true)) {
            return self::FAILURE;
        }

        foreach (BaseHelper::scanFolder(plugin_path()) as $plugin) {
            $pluginService->remove($plugin);
        }

        $this->components->info('Removed successfully!');

        return self::SUCCESS;
    }
}
