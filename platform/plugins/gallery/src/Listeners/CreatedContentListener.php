<?php

namespace Botble\Gallery\Listeners;

use Botble\Base\Events\CreatedContentEvent;
use Exception;
use Gallery;

class CreatedContentListener
{
    public function handle(CreatedContentEvent $event): void
    {
        try {
            Gallery::saveGallery($event->request, $event->data);
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
