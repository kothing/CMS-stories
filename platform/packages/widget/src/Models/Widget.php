<?php

namespace Botble\Widget\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Widget extends BaseModel
{
    protected $table = 'widgets';

    protected $fillable = [
        'widget_id',
        'sidebar_id',
        'theme',
        'position',
        'data',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    protected function position(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value >= 0 && $value < 127 ? $value : (int)substr($value, -1)
        );
    }
}
