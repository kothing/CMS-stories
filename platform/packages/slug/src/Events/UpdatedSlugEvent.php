<?php

namespace Botble\Slug\Events;

use Botble\Base\Events\Event;
use Botble\Slug\Models\Slug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class UpdatedSlugEvent extends Event
{
    use SerializesModels;

    public false|Model|null $data;

    public Slug $slug;

    public function __construct(false|Model|null $data, Slug $slug)
    {
        $this->data = $data;
        $this->slug = $slug;
    }
}
