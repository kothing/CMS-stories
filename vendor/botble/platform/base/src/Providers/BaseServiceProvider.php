<?php

namespace Botble\Base\Providers;

use App\Http\Middleware\VerifyCsrfToken;
use BaseHelper;
use Botble\Base\Exceptions\Handler;
use Botble\Base\Hooks\EmailSettingHooks;
use Botble\Base\Http\Middleware\CoreMiddleware;
use Botble\Base\Http\Middleware\DisableInDemoModeMiddleware;
use Botble\Base\Http\Middleware\HttpsProtocolMiddleware;
use Botble\Base\Http\Middleware\LocaleMiddleware;
use Botble\Base\Models\AdminNotification;
use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Repositories\Caches\MetaBoxCacheDecorator;
use Botble\Base\Repositories\Eloquent\MetaBoxRepository;
use Botble\Base\Repositories\Interfaces\MetaBoxInterface;
use Botble\Base\Supports\Action;
use Botble\Base\Supports\BreadcrumbsManager;
use Botble\Base\Supports\CustomResourceRegistrar;
use Botble\Base\Supports\Filter;
use Botble\Base\Supports\GoogleFonts;
use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Base\Widgets\AdminWidget;
use Botble\Base\Widgets\Contracts\AdminWidget as AdminWidgetContract;
use Botble\Setting\Providers\SettingServiceProvider;
use Botble\Setting\Supports\SettingStore;
use Botble\Support\Http\Middleware\BaseMiddleware;
use DateTimeZone;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\ResourceRegistrar;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use MetaBox;
use Throwable;

class BaseServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    protected bool $defer = true;

    public function register(): void
    {
        $this->app->bind(ResourceRegistrar::class, function ($app) {
            return new CustomResourceRegistrar($app['router']);
        });

        $this->setNamespace('core/base')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['general']);

        $this->app->register(SettingServiceProvider::class);

        $this->app->singleton(ExceptionHandler::class, Handler::class);

        $this->app->singleton(BreadcrumbsManager::class, BreadcrumbsManager::class);

        $this->app->bind(MetaBoxInterface::class, function () {
            return new MetaBoxCacheDecorator(new MetaBoxRepository(new MetaBoxModel()));
        });

        $this->app['config']->set([
            'session.cookie' => 'botble_session',
            'ziggy.except' => ['debugbar.*'],
            'app.debug_blacklist' => [
                '_ENV' => [
                    'APP_KEY',
                    'ADMIN_DIR',
                    'DB_DATABASE',
                    'DB_USERNAME',
                    'DB_PASSWORD',
                    'REDIS_PASSWORD',
                    'MAIL_PASSWORD',
                    'PUSHER_APP_KEY',
                    'PUSHER_APP_SECRET',
                ],
                '_SERVER' => [
                    'APP_KEY',
                    'ADMIN_DIR',
                    'DB_DATABASE',
                    'DB_USERNAME',
                    'DB_PASSWORD',
                    'REDIS_PASSWORD',
                    'MAIL_PASSWORD',
                    'PUSHER_APP_KEY',
                    'PUSHER_APP_SECRET',
                ],
                '_POST' => [
                    'password',
                ],
            ],
            'datatables-buttons.pdf_generator' => 'excel',
            'excel.exports.csv.use_bom' => true,
            'dompdf.public_path' => public_path(),
            'debugbar.enabled' => $this->app->hasDebugModeEnabled() && ! $this->app->runningInConsole() && ! $this->app->environment(['testing', 'production']),
        ]);

        $this->app->singleton('core:action', function () {
            return new Action();
        });

        $this->app->singleton('core:filter', function () {
            return new Filter();
        });

        $this->app->singleton(AdminWidgetContract::class, AdminWidget::class);

        $this->app->singleton('core:google-fonts', function (Application $app) {
            return new GoogleFonts(
                filesystem: $app->make(FilesystemManager::class)->disk('public'),
                path: 'fonts',
                inline: true,
                userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Safari/605.1.15',
            );
        });
    }

    public function boot(): void
    {
        $this
            ->loadAndPublishConfigurations(['permissions', 'assets'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadMigrations()
            ->publishAssets();

        Schema::defaultStringLength(191);

        $config = $this->app['config'];

        if ($this->app->environment('demo') || $config->get('core.base.general.disable_verify_csrf_token', false)) {
            $this->app->instance(VerifyCsrfToken::class, new BaseMiddleware());
        }

        $this->app->booted(function () use ($config) {
            do_action(BASE_ACTION_INIT);
            add_action(BASE_ACTION_META_BOXES, [MetaBox::class, 'doMetaBoxes'], 8, 2);
            add_filter(BASE_FILTER_AFTER_SETTING_EMAIL_CONTENT, [EmailSettingHooks::class, 'addEmailTemplateSettings'], 99);

            add_filter(BASE_FILTER_TOP_HEADER_LAYOUT, function ($options) {
                try {
                    $countNotificationUnread = AdminNotification::countUnread();
                } catch (Throwable) {
                    $countNotificationUnread = 0;
                }

                return $options . view('core/base::notification.notification', compact('countNotificationUnread'));
            });

            $setting = $this->app[SettingStore::class];
            $timezone = $setting->get('time_zone', $config->get('app.timezone'));
            $locale = $setting->get('locale', $config->get('core.base.general.locale', $config->get('app.locale')));

            $config->set([
                'app.locale' => $locale,
                'app.timezone' => $timezone,
            ]);

            $this->app->setLocale($locale);

            if (in_array($timezone, DateTimeZone::listIdentifiers())) {
                date_default_timezone_set($timezone);
            }
        });

        Event::listen(RouteMatched::class, function () {
            $this->registerDefaultMenus();

            /**
             * @var Router $router
             */
            $router = $this->app['router'];

            $router->pushMiddlewareToGroup('web', LocaleMiddleware::class);
            $router->pushMiddlewareToGroup('web', HttpsProtocolMiddleware::class);
            $router->aliasMiddleware('preventDemo', DisableInDemoModeMiddleware::class);
            $router->middlewareGroup('core', [CoreMiddleware::class]);
        });

        Paginator::useBootstrap();

        $forceUrl = $config->get('core.base.general.force_root_url');
        if (! empty($forceUrl)) {
            URL::forceRootUrl($forceUrl);
        }

        $forceSchema = $config->get('core.base.general.force_schema');
        if (! empty($forceSchema)) {
            $this->app['request']->server->set('HTTPS', 'on');

            URL::forceScheme($forceSchema);
        }

        $this->configureIni();

        $config->set([
            'purifier.settings' => array_merge(
                $config->get('purifier.settings', []),
                $config->get('core.base.general.purifier', [])
            ),
            'laravel-form-builder.defaults.wrapper_class' => 'form-group mb-3',
            'database.connections.mysql.strict' => $config->get('core.base.general.db_strict_mode'),
        ]);

        if (! $config->has('logging.channels.deprecations')) {
            $config->set([
                'logging.channels.deprecations' => [
                    'driver' => 'single',
                    'path' => storage_path('logs/php-deprecation-warnings.log'),
                ],
            ]);
        }

        if ($this->app->runningInConsole()) {
            AboutCommand::add('Core Information', fn () => [
                'CMS Version' => get_cms_version(),
                'Core Version' => get_core_version(),
            ]);
        }
    }

    /**
     * Add default dashboard menu for core
     */
    public function registerDefaultMenus()
    {
        dashboard_menu()
            ->registerItem([
                'id' => 'cms-core-platform-administration',
                'priority' => 999,
                'parent_id' => null,
                'name' => 'core/base::layouts.platform_admin',
                'icon' => 'fa fa-user-shield',
                'url' => null,
                'permissions' => ['users.index'],
            ])
            ->registerItem([
                'id' => 'cms-core-system-information',
                'priority' => 5,
                'parent_id' => 'cms-core-platform-administration',
                'name' => 'core/base::system.info.title',
                'icon' => null,
                'url' => route('system.info'),
                'permissions' => [ACL_ROLE_SUPER_USER],
            ])
            ->registerItem([
                'id' => 'cms-core-system-cache',
                'priority' => 6,
                'parent_id' => 'cms-core-platform-administration',
                'name' => 'core/base::cache.cache_management',
                'icon' => null,
                'url' => route('system.cache'),
                'permissions' => [ACL_ROLE_SUPER_USER],
            ]);

        if (config('core.base.general.enabled_cleanup_database', true)) {
            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-core-system-cleanup',
                    'priority' => 999,
                    'parent_id' => 'cms-core-platform-administration',
                    'name' => 'core/base::system.cleanup.title',
                    'icon' => null,
                    'url' => route('system.cleanup'),
                    'permissions' => [ACL_ROLE_SUPER_USER],
                ]);
        }

        if (config('core.base.general.enable_system_updater')) {
            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-core-system-updater',
                    'priority' => 999,
                    'parent_id' => 'cms-core-platform-administration',
                    'name' => 'core/base::system.updater',
                    'icon' => null,
                    'url' => route('system.updater'),
                    'permissions' => [ACL_ROLE_SUPER_USER],
                ]);
        }
    }

    protected function configureIni()
    {
        $currentLimit = ini_get('memory_limit');
        $currentLimitInt = Helper::convertHrToBytes($currentLimit);

        $memoryLimit = $this->app['config']->get('core.base.general.memory_limit');

        if (! $memoryLimit) {
            if (false === Helper::isIniValueChangeable('memory_limit')) {
                $memoryLimit = $currentLimit;
            } else {
                $memoryLimit = '128M';
            }
        }

        $limitInt = Helper::convertHrToBytes($memoryLimit);
        if (-1 !== $currentLimitInt && (-1 === $limitInt || $limitInt > $currentLimitInt)) {
            BaseHelper::iniSet('memory_limit', $memoryLimit);
        }
    }

    public function provides(): array
    {
        return [BreadcrumbsManager::class];
    }
}
