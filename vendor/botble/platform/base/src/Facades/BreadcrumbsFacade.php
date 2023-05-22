<?php

namespace Botble\Base\Facades;

use Botble\Base\Supports\BreadcrumbsManager;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Base\Supports\BreadcrumbsManager
 */
class BreadcrumbsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BreadcrumbsManager::class;
    }
}
