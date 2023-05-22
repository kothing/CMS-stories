<?php

namespace Botble\Media\Facades;

use Botble\Media\RvMedia;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Media\RvMedia
 */
class RvMediaFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RvMedia::class;
    }
}
