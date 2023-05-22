<?php

namespace Botble\Base\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaBox extends BaseModel
{
    protected $table = 'meta_boxes';

    protected $casts = [
        'meta_value' => 'json',
    ];

    public function reference(): BelongsTo
    {
        return $this->morphTo();
    }
}
