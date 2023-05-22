<?php

namespace Botble\ACL\Notifications;

use EmailHandler;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Throwable;

class ResetPasswordNotification extends Notification
{
    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     * @throws Throwable
     */
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        EmailHandler::setModule('acl')
            ->setVariableValue('reset_link', route('access.password.reset', ['token' => $this->token]));

        $template = 'password-reminder';
        $content = EmailHandler::prepareData(EmailHandler::getTemplateContent($template, 'core'));

        return (new MailMessage())
            ->view(['html' => new HtmlString($content)])
            ->subject(EmailHandler::getTemplateSubject($template));
    }
}
