<?php

namespace Botble\Theme\Facades;

use Botble\Theme\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Theme\Manager
 */
class ManagerFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
