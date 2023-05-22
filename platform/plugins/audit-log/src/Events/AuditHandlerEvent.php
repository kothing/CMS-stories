<?php

namespace Botble\AuditLog\Events;

use Botble\Base\Events\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;

class AuditHandlerEvent extends Event
{
    use SerializesModels;

    public string $module;

    public string $action;

    public string|int $referenceId;

    public string|int|null $referenceUser;

    public ?string $referenceName;

    public string $type;

    public function __construct(
        string $module,
        string $action,
        int $referenceId,
        ?string $referenceName,
        string $type,
        int $referenceUser = 0
    ) {
        if ($referenceUser === 0 && Auth::check()) {
            $referenceUser = Auth::id();
        }
        $this->module = $module;
        $this->action = $action;
        $this->referenceUser = $referenceUser;
        $this->referenceId = $referenceId;
        $this->referenceName = $referenceName;
        $this->type = $type;
    }
}
