<?php

namespace Botble\Base\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class BeforeEditContentEvent extends Event
{
    use SerializesModels;

    public Request $request;

    public false|Model|null $data;

    public function __construct(Request $request, $data)
    {
        $this->request = $request;
        $this->data = $data;
    }
}
