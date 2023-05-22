<?php

namespace Botble\PluginManagement;

use BaseHelper;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class PluginManifest
{
    protected string $manifestPath;

    public function __construct()
    {
        $this->manifestPath = app()->bootstrapPath('cache/plugins.php');
    }

    public function getManifestFilePath(): string
    {
        return $this->manifestPath;
    }

    public function getManifest(): array
    {
        try {
            if (File::isFile($this->manifestPath)) {
                $data = File::getRequire($this->manifestPath);

                if (count(Arr::get($data, 'namespaces', [])) == count(Arr::get($data, 'providers', [])) &&
                    count(Arr::get($data, 'namespaces', [])) == count(get_active_plugins())
                ) {
                    return $data;
                }
            }

            $data = $this->getPluginInfo();
            $this->generateManifest($data);

            return $data;
        } catch (Exception) {
            return $this->getPluginInfo();
        }
    }

    public function generateManifest(array $data = []): bool
    {
        if (! $data || ! is_array($data)) {
            $data = $this->getPluginInfo();
        }

        if (File::isWritable(File::dirname($this->manifestPath))) {
            File::replace(
                $this->manifestPath,
                '<?php return ' . var_export($data, true) . ';'
            );

            return true;
        }

        return false;
    }

    protected function getPluginInfo(): array
    {
        $namespaces = [];

        $providers = [];

        $plugins = get_active_plugins();

        foreach ($plugins as $plugin) {
            if (empty($plugin)) {
                continue;
            }

            $configFilePath = plugin_path($plugin) . '/plugin.json';

            if (! File::exists($configFilePath)) {
                continue;
            }

            $content = BaseHelper::getFileData($configFilePath);
            if (! empty($content)) {
                if (Arr::has($content, 'namespace')) {
                    $namespaces[$plugin] = $content['namespace'];
                }

                $providers[] = $content['provider'];
            }
        }

        return compact('namespaces', 'providers');
    }
}
