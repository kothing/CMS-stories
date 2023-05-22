<?php

namespace Botble\AuditLog;

use Botble\AuditLog\Events\AuditHandlerEvent;
use Eloquent;
use Illuminate\Database\Eloquent\Model;

class AuditLog
{
    public function handleEvent(string $screen, Model $data, string $action, string $type = 'info'): bool
    {
        if (! $data instanceof Eloquent || ! $data->id) {
            return false;
        }

        event(new AuditHandlerEvent($screen, $action, $data->id, $this->getReferenceName($screen, $data), $type));

        return true;
    }

    public function getReferenceName(string $screen, Model $data): string
    {
        $name = '';
        switch ($screen) {
            case USER_MODULE_SCREEN_NAME:
            case AUTH_MODULE_SCREEN_NAME:
                $name = $data->name;

                break;
            default:
                if (! empty($data)) {
                    if (isset($data->name)) {
                        $name = $data->name;
                    } elseif (isset($data->title)) {
                        $name = $data->title;
                    } elseif (isset($data->id)) {
                        $name = 'ID: ' . $data->id;
                    }
                }
        }

        return $name;
    }
}
