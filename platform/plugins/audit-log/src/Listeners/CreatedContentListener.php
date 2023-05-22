<?php

namespace Botble\AuditLog\Listeners;

use Botble\AuditLog\Events\AuditHandlerEvent;
use Botble\Base\Events\CreatedContentEvent;
use Exception;
use AuditLog;

class CreatedContentListener
{
    public function handle(CreatedContentEvent $event): void
    {
        try {
            if ($event->data->id) {
                event(new AuditHandlerEvent(
                    $event->screen,
                    'created',
                    $event->data->id,
                    AuditLog::getReferenceName($event->screen, $event->data),
                    'info'
                ));
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
