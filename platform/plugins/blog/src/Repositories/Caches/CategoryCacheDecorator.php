<?php

namespace Botble\Blog\Repositories\Caches;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Blog\Repositories\Interfaces\CategoryInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;

class CategoryCacheDecorator extends CacheAbstractDecorator implements CategoryInterface
{
    public function getDataSiteMap()
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function getFeaturedCategories($limit, array $with = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function getAllCategories(array $condition = [], array $with = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function getCategoryById($id)
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }

    public function getCategories(array $select, array $orderBy, array $conditions = ['status' => BaseStatusEnum::PUBLISHED])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function getAllRelatedChildrenIds($id)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function getAllCategoriesWithChildren(array $condition = [], array $with = [], array $select = ['*'])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function getFilters($model)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function getPopularCategories(int $limit, array $with = ['slugable'], array $withCount = ['posts'])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
