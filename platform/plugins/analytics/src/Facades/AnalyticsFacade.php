<?php

namespace Botble\Analytics\Facades;

use Botble\Analytics\Abstracts\AnalyticsAbstract;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Analytics\Analytics
 */
class AnalyticsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AnalyticsAbstract::class;
    }
}
