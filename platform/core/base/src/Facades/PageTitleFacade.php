<?php

namespace Botble\Base\Facades;

use Botble\Base\Supports\PageTitle;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Base\Supports\PageTitle
 */
class PageTitleFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PageTitle::class;
    }
}
