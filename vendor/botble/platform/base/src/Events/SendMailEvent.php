<?php

namespace Botble\Base\Events;

use Illuminate\Queue\SerializesModels;

class SendMailEvent extends Event
{
    use SerializesModels;

    public string $content;

    public string $title;

    public array|string|null $to = null;

    public array $args = [];

    public bool $debug = false;

    public function __construct(string $content, string $title, array|string|null $to, array $args, bool $debug = false)
    {
        $this->content = $content;
        $this->title = $title;
        $this->to = $to;
        $this->args = $args;
        $this->debug = $debug;
    }
}
