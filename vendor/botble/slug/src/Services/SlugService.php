<?php

namespace Botble\Slug\Services;

use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Illuminate\Support\Str;
use SlugHelper;

class SlugService
{
    protected SlugInterface $slugRepository;

    public function __construct(SlugInterface $slugRepository)
    {
        $this->slugRepository = $slugRepository;
    }

    public function create(?string $name, ?int $slugId = 0, $model = null): ?string
    {
        $slug = Str::slug($name, '-', ! SlugHelper::turnOffAutomaticUrlTranslationIntoLatin() ? 'en' : false);

        $index = 1;
        $baseSlug = $slug;

        $prefix = null;
        if (! empty($model)) {
            $prefix = SlugHelper::getPrefix($model);
        }

        while ($this->checkIfExistedSlug($slug, $slugId, $prefix)) {
            $slug = apply_filters(FILTER_SLUG_EXISTED_STRING, $baseSlug . '-' . $index++, $baseSlug, $index, $model);
        }

        if (empty($slug)) {
            $slug = time();
        }

        return apply_filters(FILTER_SLUG_STRING, $slug, $model);
    }

    protected function checkIfExistedSlug(?string $slug, ?int $slugId, ?string $prefix): bool
    {
        return $this->slugRepository
            ->getModel()
            ->where([
                'key' => $slug,
                'prefix' => $prefix,
            ])
            ->where('id', '!=', (int)$slugId)
            ->exists();
    }
}
