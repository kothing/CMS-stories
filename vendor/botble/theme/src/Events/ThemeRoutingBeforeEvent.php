<?php

namespace Botble\Theme\Events;

use Botble\Base\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Routing\Router;

class ThemeRoutingBeforeEvent extends Event
{
    use SerializesModels;

    public Router $router;

    public function __construct()
    {
        $this->router = app('router');
    }
}
