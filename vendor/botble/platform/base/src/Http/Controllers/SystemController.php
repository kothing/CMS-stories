<?php

namespace Botble\Base\Http\Controllers;

use Arr;
use Assets;
use BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Services\CleanDatabaseService;
use Botble\Base\Supports\Core;
use Botble\Base\Supports\Helper;
use Botble\Base\Supports\Language;
use Botble\Base\Supports\MembershipAuthorization;
use Botble\Base\Supports\SystemManagement;
use Botble\Base\Tables\InfoTable;
use Botble\Table\TableBuilder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Menu;

class SystemController extends Controller
{
    public function getInfo(Request $request, TableBuilder $tableBuilder)
    {
        page_title()->setTitle(trans('core/base::system.info.title'));

        Assets::addScriptsDirectly('vendor/core/core/base/js/system-info.js')
            ->addStylesDirectly(['vendor/core/core/base/css/system-info.css']);

        $composerArray = SystemManagement::getComposerArray();
        $packages = SystemManagement::getPackagesAndDependencies($composerArray['require']);

        $infoTable = $tableBuilder->create(InfoTable::class);

        if ($request->expectsJson()) {
            return $infoTable->renderTable();
        }

        $systemEnv = SystemManagement::getSystemEnv();
        $serverEnv = SystemManagement::getServerEnv();

        $requiredPhpVersion = Arr::get($composerArray, 'require.php', '^8.0.2');
        $requiredPhpVersion = str_replace('^', '', $requiredPhpVersion);
        $requiredPhpVersion = str_replace('~', '', $requiredPhpVersion);

        $matchPHPRequirement = version_compare(phpversion(), $requiredPhpVersion, '>=') > 0;

        return view(
            'core/base::system.info',
            compact(
                'packages',
                'infoTable',
                'systemEnv',
                'serverEnv',
                'matchPHPRequirement',
                'requiredPhpVersion'
            )
        );
    }

    public function getCacheManagement()
    {
        page_title()->setTitle(trans('core/base::cache.cache_management'));

        Assets::addScriptsDirectly('vendor/core/core/base/js/cache.js');

        return view('core/base::system.cache');
    }

    public function postClearCache(Request $request, BaseHttpResponse $response, Filesystem $files, Application $app)
    {
        switch ($request->input('type')) {
            case 'clear_cms_cache':
                Helper::clearCache();
                Menu::clearCacheMenuItems();
                $pluginCachePath = $app->bootstrapPath('cache/plugins.php');

                if ($files->exists($pluginCachePath)) {
                    $files->delete($pluginCachePath);
                }

                if (config('core.base.general.google_fonts_enabled_cache') && $files->isDirectory(Storage::path('fonts'))) {
                    $files->deleteDirectory(Storage::path('fonts'));
                }

                break;
            case 'refresh_compiled_views':
                foreach ($files->glob(config('view.compiled') . '/*') as $view) {
                    $files->delete($view);
                }

                break;
            case 'clear_config_cache':
                $files->delete($app->getCachedConfigPath());

                break;
            case 'clear_route_cache':
                $files->delete($app->getCachedRoutesPath());

                break;
            case 'clear_log':
                if ($files->isDirectory(storage_path('logs'))) {
                    foreach ($files->allFiles(storage_path('logs')) as $file) {
                        $files->delete($file->getPathname());
                    }
                }

                break;
        }

        return $response->setMessage(trans('core/base::cache.commands.' . $request->input('type') . '.success_msg'));
    }

    public function authorize(MembershipAuthorization $authorization, BaseHttpResponse $response)
    {
        $authorization->authorize();

        return $response;
    }

    public function getLanguage(string $lang, Request $request)
    {
        if ($lang && array_key_exists($lang, Language::getAvailableLocales())) {
            if (Auth::check()) {
                cache()->forget(md5('cache-dashboard-menu-' . $request->user()->getKey()));
            }
            session()->put('site-locale', $lang);
        }

        return redirect()->back();
    }

    public function getMenuItemsCount(BaseHttpResponse $response)
    {
        $data = apply_filters(BASE_FILTER_MENU_ITEMS_COUNT, []);

        return $response->setData($data);
    }

    public function getCheckUpdate(BaseHttpResponse $response)
    {
        if (! config('core.base.general.enable_system_updater')) {
            return $response;
        }

        $response->setData(['has_new_version' => false]);

        $api = new Core();

        $updateData = $api->checkUpdate();

        if ($updateData['status']) {
            $response
                ->setData(['has_new_version' => true])
                ->setMessage(
                    'A new version (' . $updateData['version'] . ' / released on ' . $updateData['release_date'] . ') is available to update'
                );
        }

        return $response;
    }

    public function getUpdater()
    {
        if (! config('core.base.general.enable_system_updater')) {
            abort(404);
        }

        header('Cache-Control: no-cache');

        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        page_title()->setTitle(trans('core/base::system.updater'));

        $api = new Core();

        $updateData = $api->checkUpdate();

        if ($updateData['status']) {
            $updateData['message'] = 'A new version (' . $updateData['version'] . ' / released on ' . $updateData['release_date'] . ') is available to update!';
        } else {
            $updateData['message'] = 'The system is up-to-date. There are no new versions to update!';
        }

        return view('core/base::system.updater', compact('api', 'updateData'));
    }

    public function getCleanup(
        Request $request,
        BaseHttpResponse $response,
        CleanDatabaseService $cleanDatabaseService
    ): BaseHttpResponse|View {
        if (! config('core.base.general.enabled_cleanup_database', true)) {
            abort(401);
        }

        page_title()->setTitle(trans('core/base::system.cleanup.title'));

        Assets::addScriptsDirectly('vendor/core/core/base/js/cleanup.js');

        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        $disabledTables = [
            'disabled' => $cleanDatabaseService->getIgnoreTables(),
            'checked' => [],
        ];

        if ($request->isMethod('POST')) {
            Validator::validate($request->input(), ['tables' => 'array']);

            $cleanDatabaseService->execute($request->input('tables', []));

            return $response->setMessage(trans('core/base::system.cleanup.success_message'));
        }

        return view('core/base::system.cleanup', compact('tables', 'disabledTables'));
    }
}
