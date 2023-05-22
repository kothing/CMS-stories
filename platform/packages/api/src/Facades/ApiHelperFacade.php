<?php

namespace Botble\Api\Facades;

use Botble\Api\Supports\ApiHelper;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Api\Supports\ApiHelper
 */
class ApiHelperFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ApiHelper::class;
    }
}
