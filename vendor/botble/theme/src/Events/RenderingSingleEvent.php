<?php

namespace Botble\Theme\Events;

use Botble\Base\Events\Event;
use Botble\Slug\Models\Slug;
use Illuminate\Queue\SerializesModels;

class RenderingSingleEvent extends Event
{
    use SerializesModels;

    public Slug $slug;

    public function __construct(Slug $slug)
    {
        $this->slug = $slug;
    }
}
