<?php

namespace Botble\Newsletter\Listeners;

use Botble\Newsletter\Events\SubscribeNewsletterEvent;
use EmailHandler;
use Html;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;

class SendEmailNotificationAboutNewSubscriberListener implements ShouldQueue
{
    public function handle(SubscribeNewsletterEvent $event): void
    {
        $mailer = EmailHandler::setModule(NEWSLETTER_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'newsletter_name' => $event->newsletter->name ?? 'N/A',
                'newsletter_email' => $event->newsletter->email,
                'newsletter_unsubscribe_link' => Html::link(
                    URL::signedRoute(
                        'public.newsletter.unsubscribe',
                        ['user' => $event->newsletter->id]
                    ),
                    __('here')
                )->toHtml(),
            ]);

        $mailer->sendUsingTemplate('subscriber_email', $event->newsletter->email);

        $mailer->sendUsingTemplate('admin_email');
    }
}
