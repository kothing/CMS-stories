<?php

namespace Botble\Base\Supports;

use BaseHelper;
use Illuminate\Support\Facades\File;
use Request;

class SystemManagement
{
    public static function getComposerArray(): array
    {
        return BaseHelper::getFileData(base_path('composer.json'));
    }

    public static function getPackagesAndDependencies(array $packagesArray): array
    {
        $packages = [];
        foreach ($packagesArray as $key => $value) {
            $packageFile = base_path('vendor/' . $key . '/composer.json');

            if ($key !== 'php' && File::exists($packageFile)) {
                $json2 = file_get_contents($packageFile);
                $dependenciesArray = json_decode($json2, true);
                $dependencies = array_key_exists('require', $dependenciesArray) ?
                    $dependenciesArray['require'] : 'No dependencies';
                $devDependencies = array_key_exists('require-dev', $dependenciesArray) ?
                    $dependenciesArray['require-dev'] : 'No dependencies';

                $packages[] = [
                    'name' => $key,
                    'version' => $value,
                    'dependencies' => $dependencies,
                    'dev-dependencies' => $devDependencies,
                ];
            }
        }

        return $packages;
    }

    public static function getSystemEnv(): array
    {
        return [
            'version' => app()->version(),
            'timezone' => config('app.timezone'),
            'debug_mode' => app()->hasDebugModeEnabled(),
            'storage_dir_writable' => File::isWritable(base_path('storage')),
            'cache_dir_writable' => File::isReadable(app()->bootstrapPath('cache')),
            'app_size' => BaseHelper::humanFilesize(self::folderSize(base_path())),
        ];
    }

    protected static function folderSize(string $directory): int
    {
        $size = 0;
        foreach (File::glob(rtrim($directory, '/') . '/*', GLOB_NOSORT) as $each) {
            $size += File::isFile($each) ? File::size($each) : self::folderSize($each);
        }

        return $size;
    }

    public static function getServerEnv(): array
    {
        return [
            'version' => phpversion(),
            'memory_limit' => @ini_get('memory_limit'),
            'max_execution_time' => @ini_get('max_execution_time'),
            'server_software' => Request::server('SERVER_SOFTWARE'),
            'server_os' => function_exists('php_uname') ? php_uname() : 'N/A',
            'database_connection_name' => config('database.default'),
            'ssl_installed' => self::checkSslIsInstalled(),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_connection' => config('queue.default'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'curl' => extension_loaded('curl'),
            'exif' => extension_loaded('exif'),
            'pdo' => extension_loaded('pdo'),
            'fileinfo' => extension_loaded('fileinfo'),
            'tokenizer' => extension_loaded('tokenizer'),
            'imagick_or_gd' => extension_loaded('imagick') || extension_loaded('gd'),
            'zip' => extension_loaded('zip'),
        ];
    }

    protected static function checkSslIsInstalled(): bool
    {
        return ! empty(Request::server('HTTPS')) && Request::server('HTTPS') != 'off';
    }
}
