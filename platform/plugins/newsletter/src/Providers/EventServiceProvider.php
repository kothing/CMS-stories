<?php

namespace Botble\Newsletter\Providers;

use Botble\Newsletter\Events\SubscribeNewsletterEvent;
use Botble\Newsletter\Events\UnsubscribeNewsletterEvent;
use Botble\Newsletter\Listeners\AddSubscriberToMailchimpContactListListener;
use Botble\Newsletter\Listeners\AddSubscriberToSendGridContactListListener;
use Botble\Newsletter\Listeners\RemoveSubscriberToMailchimpContactListListener;
use Botble\Newsletter\Listeners\SendEmailNotificationAboutNewSubscriberListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SubscribeNewsletterEvent::class => [
            SendEmailNotificationAboutNewSubscriberListener::class,
            AddSubscriberToMailchimpContactListListener::class,
            AddSubscriberToSendGridContactListListener::class,
        ],
        UnsubscribeNewsletterEvent::class => [
            RemoveSubscriberToMailchimpContactListListener::class,
        ],
    ];
}
