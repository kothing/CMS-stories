<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Blog\Models\Category;
use Botble\Language\Models\LanguageMeta;
use Botble\Menu\Models\Menu as MenuModel;
use Botble\Menu\Models\MenuLocation;
use Botble\Menu\Models\MenuNode;
use Botble\Page\Models\Page;
use Illuminate\Support\Arr;
use Menu;

class MenuSeeder extends BaseSeeder
{
    public function run(): void
    {
        $data = [
            'en_US' => [
                [
                    'name' => 'Main menu',
                    'slug' => 'main-menu',
                    'location' => 'main-menu',
                    'items' => [
                        [
                            'title' => 'Home',
                            'url' => '/',
                            'icon_font' => 'elegant-icon icon_house_alt mr-5',
                            'children' => [
                                [
                                    'title' => 'Home default',
                                    'url' => '/',
                                ],
                                [
                                    'title' => 'Home 2',
                                    'reference_id' => 2,
                                    'reference_type' => Page::class,
                                ],
                                [
                                    'title' => 'Home 3',
                                    'reference_id' => 3,
                                    'reference_type' => Page::class,
                                ],
                            ],
                        ],
                        [
                            'title' => 'Travel',
                            'reference_id' => 2,
                            'reference_type' => Category::class,
                        ],
                        [
                            'title' => 'Destination',
                            'reference_id' => 4,
                            'reference_type' => Category::class,
                        ],
                        [
                            'title' => 'Hotels',
                            'reference_id' => 6,
                            'reference_type' => Category::class,
                        ],
                        [
                            'title' => 'Lifestyle',
                            'reference_id' => 9,
                            'reference_type' => Category::class,
                        ],
                        [
                            'title' => 'Blog',
                            'reference_id' => 4,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title' => 'Galleries',
                            'url' => '/galleries',
                        ],
                        [
                            'title' => 'Blog layouts',
                            'url' => '/blog',
                            'children' => [
                                [
                                    'title' => 'Grid layout',
                                    'reference_id' => 9,
                                    'reference_type' => Page::class,
                                ],
                                [
                                    'title' => 'List layout',
                                    'reference_id' => 7,
                                    'reference_type' => Page::class,
                                ],
                                [
                                    'title' => 'Big layout',
                                    'reference_id' => 8,
                                    'reference_type' => Page::class,
                                ],
                            ],
                        ],
                        [
                            'title' => 'Contact',
                            'reference_id' => 5,
                            'reference_type' => Page::class,
                        ],
                    ],
                ],
                [
                    'name' => 'Quick links',
                    'slug' => 'quick-links',
                    'items' => [
                        [
                            'title' => 'Homepage',
                            'url' => '/',
                        ],
                        [
                            'title' => 'Contact',
                            'reference_id' => 5,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title' => 'Blog',
                            'reference_id' => 4,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title' => 'Travel',
                            'reference_id' => 2,
                            'reference_type' => Category::class,
                        ],
                        [
                            'title' => 'Galleries',
                            'url' => '/galleries',
                        ],
                    ],
                ],
            ],
            'zh_CN' => [
                [
                    'name' => 'Top Menu',
                    'slug' => 'top-menu',
                    'location' => 'header-menu',
                    'items' => [
                        [
                            'title' => 'contact us',
                            'reference_id' => 5,
                            'reference_type' => Page::class,
                        ],
                    ],
                    ],
                [
                    'name' => 'Menu chính',
                    'slug' => 'menu-chinh',
                    'location' => 'main-menu',
                    'items' => [
                        [
                            'title' => 'Home',
                            'url' => '/',
                            'icon_font' => 'elegant-icon icon_house_alt mr-5',
                            'children' => [
                                [
                                    'title' => 'Home default',
                                    'url' => '/',
                                ],
                                [
                                    'title' => 'Home 2',
                                    'reference_id' => 2,
                                    'reference_type' => Page::class,
                                ],
                                [
                                    'title' => 'Home 3',
                                    'reference_id' => 3,
                                    'reference_type' => Page::class,
                                ],
                            ],
                        ],
                        [
                            'title' => 'Travel',
                            'reference_id' => 2,
                            'reference_type' => Category::class,
                        ],
                        [
                            'title' => 'Destination',
                            'reference_id' => 4,
                            'reference_type' => Category::class,
                        ],
                        [
                            'title' => 'Hotels',
                            'reference_id' => 6,
                            'reference_type' => Category::class,
                        ],
                        [
                            'title' => 'Lifestyle',
                            'reference_id' => 9,
                            'reference_type' => Category::class,
                        ],
                        [
                            'title' => 'Blog',
                            'reference_id' => 4,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title' => 'Galleries',
                            'url' => '/galleries',
                        ],
                        [
                            'title' => 'Blog layouts',
                            'url' => '/blog',
                            'children' => [
                                [
                                    'title' => 'Grid layout',
                                    'reference_id' => 9,
                                    'reference_type' => Page::class,
                                ],
                                [
                                    'title' => 'List layout',
                                    'reference_id' => 7,
                                    'reference_type' => Page::class,
                                ],
                                [
                                    'title' => 'Big layout',
                                    'reference_id' => 8,
                                    'reference_type' => Page::class,
                                ],
                            ],
                        ],
                        [
                            'title' => 'Contact',
                            'reference_id' => 5,
                            'reference_type' => Page::class,
                        ],
                    ],
                ],
                [
                    'name' => 'Quick links',
                    'slug' => 'quick_links',
                    'items' => [
                        [
                            'title' => 'Homepage',
                            'url' => '/',
                        ],
                        [
                            'title' => 'Contact',
                            'reference_id' => 5,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title' => 'Blog',
                            'reference_id' => 4,
                            'reference_type' => Page::class,
                        ],
                        [
                            'title' => 'Travel',
                            'reference_id' => 2,
                            'reference_type' => Category::class,
                        ],
                        [
                            'title' => 'Galleries',
                            'url' => '/galleries',
                        ],
                    ],
                ],
            ],
        ];

        MenuModel::truncate();
        MenuLocation::truncate();
        MenuNode::truncate();
        LanguageMeta::where('reference_type', MenuModel::class)->delete();
        LanguageMeta::where('reference_type', MenuLocation::class)->delete();

        foreach ($data as $locale => $menus) {
            foreach ($menus as $index => $item) {
                $menu = MenuModel::create(Arr::except($item, ['items', 'location']));

                if (isset($item['location'])) {
                    $menuLocation = MenuLocation::create([
                        'menu_id' => $menu->id,
                        'location' => $item['location'],
                    ]);

                    $originValue = LanguageMeta::where([
                        'reference_id' => $locale == 'en_US' ? $menu->id : $menu->id + 3,
                        'reference_type' => MenuLocation::class,
                    ])->value('lang_meta_origin');

                    LanguageMeta::saveMetaData($menuLocation, $locale, $originValue);
                }

                foreach ($item['items'] as $menuNode) {
                    $this->createMenuNode($index, $menuNode, $locale, $menu->id);
                }

                $originValue = null;

                if ($locale !== 'en_US') {
                    $originValue = LanguageMeta::where([
                        'reference_id' => $index + 1,
                        'reference_type' => MenuModel::class,
                    ])->value('lang_meta_origin');
                }

                LanguageMeta::saveMetaData($menu, $locale, $originValue);
            }
        }

        Menu::clearCacheMenuItems();
    }

    /**
     * @param int $index
     * @param array $menuNode
     * @param string $locale
     * @param int $menuId
     * @param int $parentId
     */
    protected function createMenuNode(int $index, array $menuNode, string $locale, int $menuId, int $parentId = 0): void
    {
        $menuNode['menu_id'] = $menuId;
        $menuNode['parent_id'] = $parentId;

        if (isset($menuNode['url'])) {
            $menuNode['url'] = str_replace(url(''), '', $menuNode['url']);
        }

        if (Arr::has($menuNode, 'children')) {
            $children = $menuNode['children'];
            $menuNode['has_child'] = true;

            unset($menuNode['children']);
        } else {
            $children = [];
            $menuNode['has_child'] = false;
        }

        $createdNode = MenuNode::create($menuNode);

        if ($children) {
            foreach ($children as $child) {
                $this->createMenuNode($index, $child, $locale, $menuId, $createdNode->id);
            }
        }
    }
}
