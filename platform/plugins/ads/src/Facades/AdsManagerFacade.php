<?php

namespace Botble\Ads\Facades;

use Botble\Ads\Supports\AdsManager;
use Illuminate\Support\Facades\Facade;

class AdsManagerFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AdsManager::class;
    }
}
