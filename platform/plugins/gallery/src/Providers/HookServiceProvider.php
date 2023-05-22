<?php

namespace Botble\Gallery\Providers;

use Assets;
use Botble\Gallery\Services\GalleryService;
use Botble\Shortcode\Compilers\Shortcode;
use Eloquent;
use Gallery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use MetaBox;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_action(BASE_ACTION_META_BOXES, [$this, 'addGalleryBox'], 13, 2);

        if (function_exists('shortcode')) {
            add_shortcode(
                'gallery',
                trans('plugins/gallery::gallery.gallery_images'),
                trans('plugins/gallery::gallery.add_gallery_short_code'),
                [$this, 'render']
            );

            shortcode()->setAdminConfig('gallery', function ($attributes, $content) {
                return view('plugins/gallery::partials.short-code-admin-config', compact('attributes', 'content'))
                    ->render();
            });
        }

        add_filter(BASE_FILTER_PUBLIC_SINGLE_DATA, [$this, 'handleSingleView'], 11);
    }

    public function addGalleryBox(string $context, ?Model $object): void
    {
        if ($object && in_array(get_class($object), Gallery::getSupportedModules()) && $context == 'advanced') {
            Assets::addStylesDirectly(['vendor/core/plugins/gallery/css/admin-gallery.css'])
                ->addScriptsDirectly(['vendor/core/plugins/gallery/js/gallery-admin.js'])
                ->addScripts(['sortable']);

            MetaBox::addMetaBox(
                'gallery_wrap',
                trans('plugins/gallery::gallery.gallery_box'),
                [$this, 'galleryMetaField'],
                get_class($object),
                $context
            );
        }
    }

    public function galleryMetaField(): string
    {
        $value = null;
        $args = func_get_args();

        if ($args[0] && $args[0]->id) {
            $value = gallery_meta_data($args[0]);
        }

        return view('plugins/gallery::gallery-box', compact('value'))->render();
    }

    public function render(Shortcode $shortcode): string
    {
        Gallery::registerAssets();

        $view = apply_filters('galleries_box_template_view', 'plugins/gallery::gallery');

        return view($view, compact('shortcode'))->render();
    }

    public function handleSingleView(Eloquent|array $slug): Eloquent|array
    {
        return (new GalleryService())->handleFrontRoutes($slug);
    }
}
