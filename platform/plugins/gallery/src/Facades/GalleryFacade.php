<?php

namespace Botble\Gallery\Facades;

use Botble\Gallery\GallerySupport;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Gallery\GallerySupport
 */
class GalleryFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GallerySupport::class;
    }
}
