<?php

namespace Botble\Blog\Repositories\Caches;

use Botble\Blog\Repositories\Interfaces\TagInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;

class TagCacheDecorator extends CacheAbstractDecorator implements TagInterface
{
    public function getDataSiteMap()
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function getPopularTags($limit, array $with = ['slugable'], array $withCount = ['posts'])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function getAllTags($active = true)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
