<?php

namespace Botble\Base\Providers;

use Botble\Base\Events\AdminNotificationEvent;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\SendMailEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Listeners\AdminNotificationListener;
use Botble\Base\Listeners\BeforeEditContentListener;
use Botble\Base\Listeners\CreatedContentListener;
use Botble\Base\Listeners\DeletedContentListener;
use Botble\Base\Listeners\SendMailListener;
use Botble\Base\Listeners\UpdatedContentListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SendMailEvent::class => [
            SendMailListener::class,
        ],
        CreatedContentEvent::class => [
            CreatedContentListener::class,
        ],
        UpdatedContentEvent::class => [
            UpdatedContentListener::class,
        ],
        DeletedContentEvent::class => [
            DeletedContentListener::class,
        ],
        BeforeEditContentEvent::class => [
            BeforeEditContentListener::class,
        ],
        AdminNotificationEvent::class => [
            AdminNotificationListener::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();

        Event::listen(['cache:cleared'], function () {
            File::delete([storage_path('cache_keys.json'), storage_path('settings.json')]);
        });
    }
}
