<?php

namespace Botble\AuditLog\Facades;

use Botble\AuditLog\AuditLog;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Botble\AuditLog\AuditLog
 */
class AuditLogFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AuditLog::class;
    }
}
