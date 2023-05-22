<?php

namespace Botble\Ads\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\Ads\Repositories\Interfaces\AdsInterface;
use Illuminate\Database\Eloquent\Collection;

class AdsCacheDecorator extends CacheAbstractDecorator implements AdsInterface
{
    public function getAll(): Collection
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
