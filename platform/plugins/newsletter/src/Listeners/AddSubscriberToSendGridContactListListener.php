<?php

namespace Botble\Newsletter\Listeners;

use Botble\Newsletter\Events\SubscribeNewsletterEvent;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;
use SendGrid;

class AddSubscriberToSendGridContactListListener implements ShouldQueue
{
    public function handle(SubscribeNewsletterEvent $event): void
    {
        if (setting('enable_newsletter_contacts_list_api')) {
            $sendgridApiKey = setting('newsletter_sendgrid_api_key');
            $sendgridListId = setting('newsletter_sendgrid_list_id');

            if ($sendgridApiKey && $sendgridListId) {
                $sg = new SendGrid($sendgridApiKey);

                $name = explode(' ', $event->newsletter->name);

                $requestBody = json_decode(
                    '{
                        "list_ids": [
                            "' . $sendgridListId . '"
                        ],
                        "contacts": [
                            {
                                "first_name": "' . Arr::first($name) . '",
                                "last_name": "' . Arr::get($name, '1', Arr::first($name)) . '",
                                "email": "' . $event->newsletter->email . '"
                            }
                        ]
                    }'
                );

                try {
                    $sg->client->marketing()->contacts()->put($requestBody);
                } catch (Exception $exception) {
                    info('Caught exception: ' . $exception->getMessage());
                }
            }
        }
    }
}
