<?php

namespace Botble\Newsletter\Listeners;

use Botble\Newsletter\Events\SubscribeNewsletterEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Newsletter;

class RemoveSubscriberToMailchimpContactListListener implements ShouldQueue
{
    public function handle(SubscribeNewsletterEvent $event): void
    {
        if (setting('enable_newsletter_contacts_list_api')) {
            $mailchimpApiKey = setting('newsletter_mailchimp_api_key');
            $mailchimpListId = setting('newsletter_mailchimp_list_id');

            if ($mailchimpApiKey && $mailchimpListId) {
                config([
                    'newsletter.apiKey' => $mailchimpApiKey,
                    'newsletter.lists.subscribers.id' => $mailchimpListId,
                ]);

                Newsletter::unsubscribe($event->newsletter->email);
            }
        }
    }
}
