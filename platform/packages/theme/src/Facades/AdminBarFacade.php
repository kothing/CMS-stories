<?php

namespace Botble\Theme\Facades;

use Botble\Theme\Supports\AdminBar;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Theme\Supports\AdminBar
 */
class AdminBarFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AdminBar::class;
    }
}
