<?php

namespace Botble\Newsletter\Enums;

use Botble\Base\Supports\Enum;
use Html;
use Illuminate\Support\HtmlString;

/**
 * @method static NewsletterStatusEnum SUBSCRIBED()
 * @method static NewsletterStatusEnum UNSUBSCRIBED()
 */
class NewsletterStatusEnum extends Enum
{
    public const SUBSCRIBED = 'subscribed';
    public const UNSUBSCRIBED = 'unsubscribed';

    public static $langPath = 'plugins/newsletter::newsletter.statuses';

    public function toHtml(): HtmlString|string
    {
        return match ($this->value) {
            self::SUBSCRIBED => Html::tag(
                'span',
                self::SUBSCRIBED()->label(),
                ['class' => 'label-success status-label']
            )
                ->toHtml(),
            self::UNSUBSCRIBED => Html::tag(
                'span',
                self::UNSUBSCRIBED()->label(),
                ['class' => 'label-warning status-label']
            )
                ->toHtml(),
            default => parent::toHtml(),
        };
    }
}
