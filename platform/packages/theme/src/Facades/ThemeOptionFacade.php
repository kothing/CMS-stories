<?php

namespace Botble\Theme\Facades;

use Botble\Theme\ThemeOption;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Theme\ThemeOption
 */
class ThemeOptionFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ThemeOption::class;
    }
}
