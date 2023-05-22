<?php

namespace Botble\Captcha\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\Captcha\Captcha
 */
class CaptchaFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'captcha';
    }
}
