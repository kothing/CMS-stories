<?php

namespace Botble\Base\Facades;

use Botble\Base\Supports\MacroableModels;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Base\Supports\MacroableModels
 */
class MacroableModelsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MacroableModels::class;
    }
}
