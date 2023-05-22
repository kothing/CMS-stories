<?php

namespace Botble\Base\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class CreatedContentEvent extends Event
{
    use SerializesModels;

    public string $screen;

    public Request $request;

    public false|Model|null $data;

    public function __construct($screen, $request, $data)
    {
        $this->screen = $screen;
        $this->request = $request;
        $this->data = $data;
    }
}
