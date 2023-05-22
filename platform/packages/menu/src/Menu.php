<?php

namespace Botble\Menu;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Models\BaseModel;
use Botble\Menu\Models\MenuNode;
use Botble\Menu\Repositories\Eloquent\MenuRepository;
use Botble\Menu\Repositories\Interfaces\MenuInterface;
use Botble\Menu\Repositories\Interfaces\MenuNodeInterface;
use Botble\Support\Services\Cache\Cache;
use Collective\Html\HtmlBuilder;
use Exception;
use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Theme;

class Menu
{
    protected MenuInterface $menuRepository;

    protected HtmlBuilder $html;

    protected MenuNodeInterface $menuNodeRepository;

    protected Cache $cache;

    protected Repository $config;

    protected array $menuOptionModels = [];

    protected Collection|array $data = [];

    protected bool $loaded = false;

    public function __construct(
        MenuInterface $menuRepository,
        HtmlBuilder $html,
        MenuNodeInterface $menuNodeRepository,
        CacheManager $cache,
        Repository $config
    ) {
        $this->config = $config;
        $this->menuRepository = $menuRepository;
        $this->html = $html;
        $this->menuNodeRepository = $menuNodeRepository;
        $this->cache = new Cache($cache, MenuRepository::class);
        $this->data = collect();
    }

    public function hasMenu(string $slug, bool $active): bool
    {
        return $this->menuRepository->findBySlug($slug, $active);
    }

    public function recursiveSaveMenu(array $menuNodes, int $menuId, int $parentId): array
    {
        try {
            foreach ($menuNodes as &$row) {
                $child = Arr::get($row, 'children', []);

                foreach ($child as $index => $item) {
                    $child[$index]['menuItem']['position'] = $index;
                }

                $hasChild = ! empty($child);

                $row['menuItem'] = $this->saveMenuNode($row['menuItem'], $menuId, $parentId, $hasChild);

                if (! empty($child)) {
                    $this->recursiveSaveMenu($child, $menuId, $row['menuItem']['id']);
                }
            }

            return $menuNodes;
        } catch (Exception) {
            return [];
        }
    }

    protected function saveMenuNode(array $menuItem, int $menuId, int $parentId, bool $hasChild = false): array
    {
        $item = $this->menuNodeRepository->findById(Arr::get($menuItem, 'id'));

        $created = false;

        if (! $item) {
            $item = $this->menuNodeRepository->getModel();
            $created = true;
        }

        $item->fill($menuItem);
        $item->menu_id = $menuId;
        $item->parent_id = $parentId;
        $item->has_child = $hasChild;

        $item = $this->getReferenceMenuNode($menuItem, $item);

        $this->menuNodeRepository->createOrUpdate($item);

        $menuItem['id'] = $item->id;

        if ($created) {
            event(new CreatedContentEvent(MENU_NODE_MODULE_SCREEN_NAME, request(), $item));
        } else {
            event(new UpdatedContentEvent(MENU_NODE_MODULE_SCREEN_NAME, request(), $item));
        }

        return $menuItem;
    }

    public function getReferenceMenuNode(array $item, MenuNode $menuNode): MenuNode
    {
        switch (Arr::get($item, 'reference_type')) {
            case 'custom-link':
            case '':
                $menuNode->reference_id = 0;
                $menuNode->reference_type = null;
                $menuNode->url = str_replace('&amp;', '&', Arr::get($item, 'url'));

                break;

            default:
                $menuNode->reference_id = (int)Arr::get($item, 'reference_id');
                $menuNode->reference_type = Arr::get($item, 'reference_type');

                if (class_exists($menuNode->reference_type)) {
                    $reference = $menuNode->reference_type::find($menuNode->reference_id);
                    if ($reference) {
                        $menuNode->url = str_replace(url(''), '', $reference->url);
                    }
                }

                break;
        }

        return $menuNode;
    }

    public function addMenuLocation(string $location, string $description): self
    {
        $locations = $this->getMenuLocations();
        $locations[$location] = $description;

        $this->config->set('packages.menu.general.locations', $locations);

        return $this;
    }

    public function getMenuLocations(): array
    {
        return $this->config->get('packages.menu.general.locations', []);
    }

    public function removeMenuLocation(string $location): self
    {
        $locations = $this->getMenuLocations();
        Arr::forget($locations, $location);

        $this->config->set('packages.menu.general.locations', $locations);

        return $this;
    }

    public function renderMenuLocation(string $location, array $attributes = []): string
    {
        $this->load();

        $html = '';

        foreach ($this->data as $menu) {
            if (! in_array($location, $menu->locations->pluck('location')->all())) {
                continue;
            }

            $attributes['slug'] = $menu->slug;
            $html .= $this->generateMenu($attributes);
        }

        return $html;
    }

    public function isLocationHasMenu(string $location): bool
    {
        $this->load();

        foreach ($this->data as $menu) {
            if (in_array($location, $menu->locations->pluck('location')->all())) {
                return true;
            }
        }

        return false;
    }

    public function load(bool $force = false): void
    {
        if (! $this->loaded || $force) {
            $this->data = $this->read();
            $this->loaded = true;
        }
    }

    protected function read(): Collection
    {
        $with = [
            'menuNodes',
            'menuNodes.child',
            'menuNodes.metadata',
            'locations',
        ];

        return $this->menuRepository->allBy(['status' => BaseStatusEnum::PUBLISHED], $with);
    }

    public function generateMenu(array $args = []): ?string
    {
        $this->load();

        $view = Arr::get($args, 'view');
        $theme = Arr::get($args, 'theme', true);

        $menu = Arr::get($args, 'menu');

        $slug = Arr::get($args, 'slug');
        if (! $menu && ! $slug) {
            return null;
        }

        $parentId = Arr::get($args, 'parent_id', 0);

        if (! $menu) {
            $menu = $this->data->where('slug', $slug)->first();
        }

        if (! $menu) {
            return null;
        }

        if (! Arr::has($args, 'menu_nodes')) {
            $menuNodes = $menu->menuNodes->where('parent_id', $parentId);
        } else {
            $menuNodes = Arr::get($args, 'menu_nodes', []);
        }

        if ($menuNodes instanceof Collection) {
            $menuNodes = $menuNodes->sortBy('position');
        } else {
            $menuNodes->loadMissing('reference');
        }

        $data = [
            'menu' => $menu,
            'menu_nodes' => $menuNodes,
        ];

        $data['options'] = $this->html->attributes(Arr::get($args, 'options', []));

        if ($theme && $view) {
            return Theme::partial($view, $data);
        }

        if ($view) {
            return view($view, $data)->render();
        }

        return view('packages/menu::partials.default', $data)->render();
    }

    public function registerMenuOptions(string $model, string $name): void
    {
        $options = Menu::generateSelect([
            'model' => new $model(),
            'options' => [
                'class' => 'list-item',
            ],
        ]);

        echo view('packages/menu::menu-options', compact('options', 'name'));
    }

    public function generateSelect(array $args = []): ?string
    {
        /**
         * @var BaseModel $model
         */
        $model = Arr::get($args, 'model');

        $options = $this->html->attributes(Arr::get($args, 'options', []));

        if (! Arr::has($args, 'items')) {
            if (method_exists($model, 'children')) {
                $items = $model
                    ->where('parent_id', Arr::get($args, 'parent_id', 0))
                    ->with(['children', 'children.children'])
                    ->orderBy('name', 'asc');
            } else {
                $items = $model->orderBy('name');
            }

            if (Arr::get($args, 'active', true)) {
                $items = $items->where('status', BaseStatusEnum::PUBLISHED);
            }

            $items = apply_filters(BASE_FILTER_BEFORE_GET_ADMIN_LIST_ITEM, $items, $model, get_class($model))->get();
        } else {
            $items = Arr::get($args, 'items', []);
        }

        if (empty($items)) {
            return null;
        }

        return view('packages/menu::partials.select', compact('items', 'model', 'options'))->render();
    }

    public function addMenuOptionModel(string $model): self
    {
        $this->menuOptionModels[] = $model;

        return $this;
    }

    public function getMenuOptionModels(): array
    {
        return $this->menuOptionModels;
    }

    public function setMenuOptionModels(array $models): self
    {
        $this->menuOptionModels = $models;

        return $this;
    }

    public function clearCacheMenuItems(): self
    {
        try {
            $nodes = $this->menuNodeRepository->all();

            foreach ($nodes as $node) {
                if (! $node->reference_type ||
                    ! class_exists($node->reference_type) ||
                    ! $node->reference_id ||
                    ! $node->reference
                ) {
                    continue;
                }

                $node->url = str_replace(url(''), '', $node->reference->url);
                $node->save();
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }

        return $this;
    }
}
