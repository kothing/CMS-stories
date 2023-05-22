<?php

namespace Botble\Newsletter\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Newsletter\Models\Newsletter;
use Botble\Newsletter\Repositories\Caches\NewsletterCacheDecorator;
use Botble\Newsletter\Repositories\Eloquent\NewsletterRepository;
use Botble\Newsletter\Repositories\Interfaces\NewsletterInterface;
use EmailHandler;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\ServiceProvider;
use Newsletter as MailchimpNewsletter;
use SendGrid;

class NewsletterServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->singleton(NewsletterInterface::class, function () {
            return new NewsletterCacheDecorator(
                new NewsletterRepository(new Newsletter())
            );
        });
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/newsletter')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions', 'email'])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadMigrations();

        $this->app->register(EventServiceProvider::class);

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()->registerItem([
                'id' => 'cms-plugins-newsletter',
                'priority' => 6,
                'parent_id' => null,
                'name' => 'plugins/newsletter::newsletter.name',
                'icon' => 'far fa-newspaper',
                'url' => route('newsletter.index'),
                'permissions' => ['newsletter.index'],
            ]);

            EmailHandler::addTemplateSettings(NEWSLETTER_MODULE_SCREEN_NAME, config('plugins.newsletter.email', []));
        });

        add_filter(BASE_FILTER_AFTER_SETTING_CONTENT, [$this, 'addSettings'], 249);
    }

    public function addSettings(?string $data = null): string
    {
        $mailchimpContactList = [];
        $mailchimpApiKey = setting('newsletter_mailchimp_api_key');

        if ($mailchimpApiKey) {
            try {
                $list = MailchimpNewsletter::getApi()->get('lists');

                $results = Arr::get($list, 'lists');

                if (! setting('newsletter_mailchimp_list_id')) {
                    setting()->set(['newsletter_mailchimp_list_id' => Arr::first($results, 'id')])->save();
                }

                foreach ($results as $result) {
                    $mailchimpContactList[$result['id']] = $result['name'];
                }
            } catch (Exception $exception) {
                info('Caught exception: ' . $exception->getMessage());
            }
        }

        $sendGridContactList = [];

        $sendgridApiKey = setting('newsletter_sendgrid_api_key');
        if ($sendgridApiKey) {
            $sg = new SendGrid($sendgridApiKey);

            try {
                $list = $sg->client->marketing()->lists()->get();

                $results = Arr::get(json_decode($list->body(), true), 'result');

                if (! setting('newsletter_sendgrid_list_id')) {
                    setting()->set(['newsletter_sendgrid_list_id' => Arr::first($results, 'id')])->save();
                }

                foreach ($results as $result) {
                    $sendGridContactList[$result['id']] = $result['name'];
                }
            } catch (Exception $exception) {
                info('Caught exception: ' . $exception->getMessage());
            }
        }

        return $data . view('plugins/newsletter::setting', compact('mailchimpContactList', 'sendGridContactList'))
                ->render();
    }
}
