<?php

namespace Botble\Dashboard\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardWidget extends BaseModel
{
    protected $table = 'dashboard_widgets';

    protected $fillable = [
        'name',
    ];

    public function settings(): HasMany
    {
        return $this->hasMany(DashboardWidgetSetting::class, 'widget_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (DashboardWidget $widget) {
            DashboardWidgetSetting::where('widget_id', $widget->id)->delete();
        });
    }
}
