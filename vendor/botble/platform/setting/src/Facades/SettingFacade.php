<?php

namespace Botble\Setting\Facades;

use Botble\Setting\Supports\SettingStore;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Setting\Supports\SettingStore
 */
class SettingFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SettingStore::class;
    }
}
