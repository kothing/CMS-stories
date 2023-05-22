<?php

namespace Botble\RssFeed\Facades;

use Botble\RssFeed\Supports\RssFeed;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\RssFeed\Supports\RssFeed
 */
class RssFeedFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RssFeed::class;
    }
}
