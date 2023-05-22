<?php

namespace Botble\Slug\Providers;

use Assets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use SlugHelper;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(BASE_FILTER_SLUG_AREA, [$this, 'addSlugBox'], 17, 2);
    }

    public function addSlugBox(?string $html = null, ?Model $object = null): ?string
    {
        if ($object && SlugHelper::isSupportedModel(get_class($object))) {
            Assets::addScriptsDirectly('vendor/core/packages/slug/js/slug.js')
                ->addStylesDirectly('vendor/core/packages/slug/css/slug.css');

            $prefix = SlugHelper::getPrefix(get_class($object));

            return $html . view('packages/slug::partials.slug', compact('object', 'prefix'))->render();
        }

        return $html;
    }
}
