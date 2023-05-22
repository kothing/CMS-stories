<?php

namespace Botble\PluginManagement\Http\Controllers;

use Assets;
use BaseHelper;
use Botble\Base\Supports\MarketplaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MarketplaceController extends Controller
{
    public function index()
    {
        page_title()->setTitle(trans('packages/plugin-management::plugin.plugins_add_new'));

        Assets::addScriptsDirectly('vendor/core/packages/plugin-management/js/marketplace.js')
            ->usingVueJS();

        return view('packages/plugin-management::marketplace.index');
    }

    public function list(Request $request, MarketplaceService $marketplaceService)
    {
        $request->merge([
            'type' => 'plugin',
            'per_page' => 12,
            'core_version' => get_core_version(),
        ]);

        $response = $marketplaceService->callApi('get', '/products', $request->input());

        if ($response instanceof JsonResponse) {
            return $response;
        }

        return $response->json();
    }

    public function detail(string $id, MarketplaceService $marketplaceService)
    {
        $response = $marketplaceService->callApi('get', '/products/' . $id);

        if ($response instanceof JsonResponse) {
            return $response;
        }

        return $response->json();
    }

    public function iframe(string $id, MarketplaceService $marketplaceService)
    {
        $response = $marketplaceService->callApi('get', '/products/' . $id . '/iframe');

        if ($response instanceof JsonResponse) {
            return $response;
        }

        return $response->body();
    }

    public function install(string $id, MarketplaceService $marketplaceService): JsonResponse
    {
        $detail = $this->detail($id, $marketplaceService);

        $version = $detail['data']['minimum_core_version'];
        if (version_compare($version, get_core_version(), '>')) {
            return response()->json([
                'error' => true,
                'message' => trans('packages/plugin-management::marketplace.minimum_core_version_error', compact('version')),
            ]);
        }

        $name = Str::afterLast($detail['data']['package_name'], '/');

        $marketplaceService->beginInstall($id, 'plugin', $name);

        return response()->json([
            'error' => false,
            'message' => trans('packages/plugin-management::marketplace.install_success'),
            'data' => [
                'name' => $name,
                'id' => $id,
            ],
        ]);
    }

    public function update(string $id, MarketplaceService $marketplaceService): JsonResponse
    {
        $detail = $this->detail($id, $marketplaceService);

        $name = Str::afterLast($detail['data']['package_name'], '/');

        $marketplaceService->beginInstall($id, 'plugin', $name);

        return response()->json([
            'error' => false,
            'message' => trans('packages/plugin-management::marketplace.update_success'),
            'data' => [
                'name' => $name,
                'id' => $id,
            ],
        ]);
    }

    public function checkUpdate(MarketplaceService $marketplaceService)
    {
        $installedPlugins = [];
        $plugins = BaseHelper::scanFolder(plugin_path());

        if (! empty($plugins)) {
            foreach ($plugins as $plugin) {
                $path = plugin_path($plugin);
                $pluginJson = $path . '/plugin.json';

                if (! File::isDirectory($path) || ! File::exists($pluginJson)) {
                    continue;
                }

                $getInfoPlugin = BaseHelper::getFileData($pluginJson);

                if (! empty($getInfoPlugin['id'])) {
                    $installedPlugins[$getInfoPlugin['id']] = $getInfoPlugin['version'];
                }
            }
        }

        if (! $installedPlugins) {
            return response()->json();
        }

        $core = BaseHelper::getFileData(core_path('core.json'));
        $productId = $core ? $core['productId'] : null;

        $response = $marketplaceService->callApi('post', '/products/check-update', [
            'products' => $installedPlugins,
            'product_id' => $productId ,
        ]);

        if ($response instanceof JsonResponse) {
            return $response;
        }

        return $response->json();
    }
}
