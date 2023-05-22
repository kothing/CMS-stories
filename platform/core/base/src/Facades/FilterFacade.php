<?php

namespace Botble\Base\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Base\Supports\Filter
 */
class FilterFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'core:filter';
    }
}
