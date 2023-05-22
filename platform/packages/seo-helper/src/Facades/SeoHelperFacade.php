<?php

namespace Botble\SeoHelper\Facades;

use Botble\SeoHelper\SeoHelper;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\SeoHelper\SeoHelper
 * @since 02/12/2015 14:08 PM
 */
class SeoHelperFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SeoHelper::class;
    }
}
