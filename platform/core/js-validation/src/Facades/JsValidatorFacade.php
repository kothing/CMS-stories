<?php

namespace Botble\JsValidation\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\JsValidation\JsValidatorFactory
 */
class JsValidatorFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'js-validator';
    }
}
