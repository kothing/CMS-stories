<?php

namespace Botble\Menu\Repositories\Caches;

use Botble\Menu\Repositories\Interfaces\MenuNodeInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Illuminate\Database\Eloquent\Collection;

class MenuNodeCacheDecorator extends CacheAbstractDecorator implements MenuNodeInterface
{
    public function getByMenuId(int $menuId, ?int $parentId, array $select = ['*'], array $with = ['child']): Collection
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
