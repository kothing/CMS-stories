<?php

namespace Botble\Base\Facades;

use Botble\Base\Supports\MetaBox;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Base\Supports\MetaBox
 */
class MetaBoxFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MetaBox::class;
    }
}
