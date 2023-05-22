<?php

namespace Botble\Base\Events;

use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class UpdatedContentEvent extends Event
{
    use SerializesModels;

    public string $screen;

    public Request $request;

    public false|Model|null $data;

    public function __construct(string|Model $screen, Request $request, $data)
    {
        if ($screen instanceof Model) {
            $screen = $screen->getTable();
        }

        $this->screen = $screen;
        $this->request = $request;
        $this->data = $data;
    }
}
