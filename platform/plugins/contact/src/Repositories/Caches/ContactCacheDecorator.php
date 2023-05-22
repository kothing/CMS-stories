<?php

namespace Botble\Contact\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\Contact\Repositories\Interfaces\ContactInterface;
use Illuminate\Database\Eloquent\Collection;

class ContactCacheDecorator extends CacheAbstractDecorator implements ContactInterface
{
    public function getUnread(array $select = ['*']): Collection
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function countUnread(): int
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
