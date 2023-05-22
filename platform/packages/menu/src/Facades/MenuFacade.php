<?php

namespace Botble\Menu\Facades;

use Botble\Menu\Menu;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Menu\Menu
 */
class MenuFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Menu::class;
    }
}
