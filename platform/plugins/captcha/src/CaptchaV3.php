<?php

namespace Botble\Captcha;

use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Theme;

class CaptchaV3
{
    protected ?string $secret;

    protected ?string $siteKey;

    protected ?string $origin;

    protected bool $rendered = false;

    public function __construct(Application $app)
    {
        $this->secret = $app['config']->get('plugins.captcha.general.secret');
        $this->siteKey = $app['config']->get('plugins.captcha.general.site_key');
        $this->origin = 'https://www.google.com/recaptcha';
    }

    public function verify(string $token, string $clientIp, array $parameters = []): bool
    {
        $client = new Client();

        $response = $client->request('POST', $this->origin . '/api/siteverify', [
            'form_params' => [
                'secret' => $this->secret,
                'response' => $token,
                'remoteip' => $clientIp,
            ],
        ]);

        $body = json_decode($response->getBody(), true);

        if (! isset($body['success']) || $body['success'] !== true) {
            return false;
        }

        $action = $parameters[0];
        $minScore = isset($parameters[1]) ? (float)$parameters[1] : 0.5;

        if ($action && (! isset($body['action']) || $action != $body['action'])) {
            return false;
        }

        $score = $body['score'] ?? false;

        return $score && $score >= $minScore;
    }

    public function display(array $attributes = ['action' => 'form'], array $options = ['name' => 'g-recaptcha-response']): ?string
    {
        if (! $this->siteKey) {
            return null;
        }

        $name = Arr::get($options, 'name', 'g-recaptcha-response');

        $fieldId = uniqid($name . '-');
        $action = Arr::get($attributes, 'action', 'form');

        $input = '<input type="hidden" name="' . $name . '" id="' . $fieldId . '">';

        if (! $this->rendered && Arr::get($attributes, 'add-js', true)) {
            $this->initJs($fieldId, $action);
        }

        $html = "var onloadCallback = function() { grecaptcha.ready(function() { refreshRecaptcha('" . $fieldId . "'); }); };";

        Theme::asset()->container('after_footer')->writeScript('google-recaptcha-' . $fieldId, $html, ['google-recaptcha']);

        $this->rendered = true;

        return $input;
    }

    public function initJs($fieldId = null, $action = 'form'): void
    {
        if ($fieldId && $action) {
            $script = "
                var refreshRecaptcha = function (fieldId) {
                   if (!fieldId) {
                       fieldId = '" . $fieldId . "';
                   }

                   var field = document.getElementById(fieldId);

                   if (field) {
                      grecaptcha.execute('" . $this->siteKey . "', {action: '" . $action . "'}).then(function(token) {
                         field.value = token;
                      });
                   }
               };";

            Theme::asset()
                ->container('after_footer')
                ->writeScript('google-recaptcha-script-' . $fieldId, $script, ['google-recaptcha']);
        }

        Theme::asset()
            ->container('after_footer')
            ->add('google-recaptcha', $this->origin . '/api.js?onload=onloadCallback&render=' . $this->siteKey . '&hl=' . app()->getLocale(), [], ['async', 'defer']);
    }
}
