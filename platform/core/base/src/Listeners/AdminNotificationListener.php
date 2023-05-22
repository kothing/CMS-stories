<?php

namespace Botble\Base\Listeners;

use Botble\Base\Events\AdminNotificationEvent;
use Botble\Base\Models\AdminNotification;

class AdminNotificationListener
{
    public function handle(AdminNotificationEvent $event): void
    {
        $item = $event->item;

        AdminNotification::create([
            'title' => $item->getTitle(),
            'action_label' => $item->getLabel(),
            'action_url' => $item->getRoute(),
            'description' => $item->getDescription(),
        ]);
    }
}
