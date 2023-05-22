<?php

namespace Botble\Contact\Events;

use Botble\Base\Events\Event;
use Eloquent;
use Illuminate\Queue\SerializesModels;

class SentContactEvent extends Event
{
    use SerializesModels;

    /**
     * @var Eloquent|false
     */
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
