<?php

namespace Botble\Widget\Facades;

use Botble\Widget\WidgetGroup;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Widget\Factories\WidgetFactory
 */
class WidgetFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'botble.widget';
    }

    public static function group(string $name): WidgetGroup
    {
        return app('botble.widget-group-collection')->group($name);
    }
}
