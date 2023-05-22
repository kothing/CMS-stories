<?php

namespace Botble\Slug\Facades;

use Botble\Slug\SlugHelper;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Slug\SlugHelper
 */
class SlugHelperFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SlugHelper::class;
    }
}
