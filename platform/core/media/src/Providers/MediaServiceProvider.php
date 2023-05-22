<?php

namespace Botble\Media\Providers;

use Aws\S3\S3Client;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Media\Chunks\Storage\ChunkStorage;
use Botble\Media\Commands\ClearChunksCommand;
use Botble\Media\Commands\DeleteThumbnailCommand;
use Botble\Media\Commands\GenerateThumbnailCommand;
use Botble\Media\Commands\InsertWatermarkCommand;
use Botble\Media\Facades\RvMediaFacade;
use Botble\Media\Models\MediaFile;
use Botble\Media\Models\MediaFolder;
use Botble\Media\Models\MediaSetting;
use Botble\Media\Repositories\Caches\MediaFileCacheDecorator;
use Botble\Media\Repositories\Caches\MediaFolderCacheDecorator;
use Botble\Media\Repositories\Caches\MediaSettingCacheDecorator;
use Botble\Media\Repositories\Eloquent\MediaFileRepository;
use Botble\Media\Repositories\Eloquent\MediaFolderRepository;
use Botble\Media\Repositories\Eloquent\MediaSettingRepository;
use Botble\Media\Repositories\Interfaces\MediaFileInterface;
use Botble\Media\Repositories\Interfaces\MediaFolderInterface;
use Botble\Media\Repositories\Interfaces\MediaSettingInterface;
use Botble\Media\Storage\BunnyCDN\BunnyCDNAdapter;
use Botble\Media\Storage\BunnyCDN\BunnyCDNClient;
use Botble\Setting\Supports\SettingStore;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Filesystem\AwsS3V3Adapter as IlluminateAwsS3V3Adapter;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use RvMedia;

/**
 * @since 02/07/2016 09:50 AM
 */
class MediaServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(MediaFileInterface::class, function () {
            return new MediaFileCacheDecorator(
                new MediaFileRepository(new MediaFile()),
                MEDIA_GROUP_CACHE_KEY
            );
        });

        $this->app->bind(MediaFolderInterface::class, function () {
            return new MediaFolderCacheDecorator(
                new MediaFolderRepository(new MediaFolder()),
                MEDIA_GROUP_CACHE_KEY
            );
        });

        $this->app->bind(MediaSettingInterface::class, function () {
            return new MediaSettingCacheDecorator(
                new MediaSettingRepository(new MediaSetting()),
                MEDIA_GROUP_CACHE_KEY
            );
        });

        AliasLoader::getInstance()->alias('RvMedia', RvMediaFacade::class);
    }

    public function boot(): void
    {
        $this->setNamespace('core/media')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions', 'media'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes()
            ->publishAssets();

        Storage::extend('wasabi', function ($app, $config) {
            $config['url'] = 'https://' . $config['bucket'] . '.s3.' . $config['region'] . '.wasabisys.com/';

            $client = new S3Client([
                'endpoint' => $config['url'],
                'bucket_endpoint' => true,
                'credentials' => [
                    'key' => $config['key'],
                    'secret' => $config['secret'],
                ],
                'region' => $config['region'],
                'version' => 'latest',
            ]);

            $adapter = new AwsS3V3Adapter($client, $config['bucket'], trim($config['root'], '/'));

            return new IlluminateAwsS3V3Adapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config,
                $client,
            );
        });

        Storage::extend('bunnycdn', function ($app, $config) {
            $adapter = new BunnyCDNAdapter(
                new BunnyCDNClient(
                    $config['storage_zone'],
                    $config['api_key'],
                    $config['region']
                ),
                'https://' . $config['hostname']
            );

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });

        $config = $this->app->make('config');
        $setting = $this->app->make(SettingStore::class);

        $config->set([
            'filesystems.default' => $setting->get('media_driver', 'public'),
            'filesystems.disks.s3' => [
                'driver' => 's3',
                'visibility' => 'public',
                'key' => $setting->get('media_aws_access_key_id', $config->get('filesystems.disks.s3.key')),
                'secret' => $setting->get('media_aws_secret_key', $config->get('filesystems.disks.s3.secret')),
                'region' => $setting->get('media_aws_default_region', $config->get('filesystems.disks.s3.region')),
                'bucket' => $setting->get('media_aws_bucket', $config->get('filesystems.disks.s3.bucket')),
                'url' => $setting->get('media_aws_url', $config->get('filesystems.disks.s3.url')),
                'endpoint' => $setting->get('media_aws_endpoint', $config->get('filesystems.disks.s3.endpoint')) ?: null,
                'use_path_style_endpoint' => $config->get('filesystems.disks.s3.use_path_style_endpoint'),
            ],
            'filesystems.disks.do_spaces' => [
                'driver' => 's3',
                'visibility' => 'public',
                'key' => $setting->get('media_do_spaces_access_key_id'),
                'secret' => $setting->get('media_do_spaces_secret_key'),
                'region' => $setting->get('media_do_spaces_default_region'),
                'bucket' => $setting->get('media_do_spaces_bucket'),
                'endpoint' => $setting->get('media_do_spaces_endpoint'),
            ],
            'filesystems.disks.wasabi' => [
                'driver' => 'wasabi',
                'visibility' => 'public',
                'key' => $setting->get('media_wasabi_access_key_id'),
                'secret' => $setting->get('media_wasabi_secret_key'),
                'region' => $setting->get('media_wasabi_default_region'),
                'bucket' => $setting->get('media_wasabi_bucket'),
                'root' => $setting->get('media_wasabi_root', '/'),
            ],
            'filesystems.disks.bunnycdn' => [
                'driver' => 'bunnycdn',
                'visibility' => 'public',
                'hostname' => $setting->get('media_bunnycdn_hostname'),
                'storage_zone' => $setting->get('media_bunnycdn_zone'),
                'url' => $setting->get('media_bunnycdn_pull_zone_url'),
                'api_key' => $setting->get('media_bunnycdn_key'),
                'region' => $setting->get('media_bunnycdn_region'),
            ],
            'core.media.media.chunk.enabled' => (bool)$setting->get(
                'media_chunk_enabled',
                $config->get('core.media.media.chunk.enabled')
            ),
            'core.media.media.chunk.chunk_size' => (int)$setting->get(
                'media_chunk_size',
                $config->get('core.media.media.chunk.chunk_size')
            ),
            'core.media.media.chunk.max_file_size' => (int)$setting->get(
                'media_max_file_size',
                $config->get('core.media.media.chunk.max_file_size')
            ),
        ]);

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()->registerItem([
                'id' => 'cms-core-media',
                'priority' => 995,
                'parent_id' => null,
                'name' => 'core/media::media.menu_name',
                'icon' => 'far fa-images',
                'url' => route('media.index'),
                'permissions' => ['media.index'],
            ]);
        });

        $this->commands([
            GenerateThumbnailCommand::class,
            DeleteThumbnailCommand::class,
            InsertWatermarkCommand::class,
        ]);

        $this->app->booted(function () {
            if (RvMedia::getConfig('chunk.clear.schedule.enabled')) {
                $schedule = $this->app->make(Schedule::class);

                $schedule->command('cms:media:chunks:clear')->cron(RvMedia::getConfig('chunk.clear.schedule.cron'));
            }
        });

        if (RvMedia::getConfig('chunk.clear.schedule.enabled')) {
            $this->commands([
                ClearChunksCommand::class,
            ]);

            $this->app->singleton(ChunkStorage::class, function () {
                return new ChunkStorage();
            });
        }
    }
}
