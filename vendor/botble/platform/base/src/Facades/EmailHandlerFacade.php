<?php

namespace Botble\Base\Facades;

use Botble\Base\Supports\EmailHandler;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Base\Supports\EmailHandler
 */
class EmailHandlerFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return EmailHandler::class;
    }
}
