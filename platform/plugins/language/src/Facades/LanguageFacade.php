<?php

namespace Botble\Language\Facades;

use Botble\Language\LanguageManager;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Language\LanguageManager
 */
class LanguageFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LanguageManager::class;
    }
}
