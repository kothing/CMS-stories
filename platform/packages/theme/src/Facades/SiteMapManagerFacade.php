<?php

namespace Botble\Theme\Facades;

use Botble\Theme\Supports\SiteMapManager;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Theme\Supports\SiteMapManager
 */
class SiteMapManagerFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SiteMapManager::class;
    }
}
