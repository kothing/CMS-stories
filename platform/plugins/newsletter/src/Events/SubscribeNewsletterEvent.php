<?php

namespace Botble\Newsletter\Events;

use Botble\Newsletter\Models\Newsletter;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscribeNewsletterEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Newsletter $newsletter;

    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }
}
