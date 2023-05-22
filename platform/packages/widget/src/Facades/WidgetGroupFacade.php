<?php

namespace Botble\Widget\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Widget\WidgetGroupCollection
 */
class WidgetGroupFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'botble.widget-group-collection';
    }
}
