<?php

namespace Botble\Base\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AdminNotification extends BaseModel
{
    use MassPrunable;

    protected $table = 'admin_notifications';

    protected $fillable = [
        'title',
        'action_label',
        'action_url',
        'description',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function markAsRead(): void
    {
        $this->update([
            'read_at' => Carbon::now(),
        ]);
    }

    public function prunable(): Builder
    {
        return $this->whereDate('created_at', '>', Carbon::now()->subDays(30)->toDateString());
    }

    public function isAbleToAccess(): bool
    {
        $route = collect(Route::getRoutes())->first(function ($route) {
            return $route->matches(request()->create($this->action_url));
        });

        $routeName = $route ? $route->getName() : null;

        return ! $routeName || Auth::user()->hasPermission($routeName);
    }

    public static function countUnread(): int
    {
        $notificationUnread = AdminNotification::query()
            ->whereNull('read_at')
            ->select('action_url')
            ->get();

        foreach ($notificationUnread as $key => $notification) {
            if (! $notification->isAbleToAccess()) {
                $notificationUnread->forget($key);
            }
        }

        return $notificationUnread->count();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (AdminNotification $notification) {
            if ($notification->action_url) {
                $notification->action_url = str_replace(url(''), '', $notification->action_url);
            }
        });
    }
}
