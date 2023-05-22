<?php

namespace Botble\SeoHelper\Listeners;

use Botble\Base\Events\UpdatedContentEvent;
use Exception;
use SeoHelper;

class UpdatedContentListener
{
    public function handle(UpdatedContentEvent $event): void
    {
        try {
            SeoHelper::saveMetaData($event->screen, $event->request, $event->data);
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
