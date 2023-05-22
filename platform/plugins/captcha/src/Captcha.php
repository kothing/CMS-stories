<?php

namespace Botble\Captcha;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use ReCaptcha\ReCaptcha;

class Captcha
{
    public const CAPTCHA_CLIENT_API = 'https://www.google.com/recaptcha/api.js';

    protected string $callbackName = 'buzzNoCaptchaOnLoadCallback';

    protected string $widgetIdName = 'buzzNoCaptchaWidgetIds';

    protected array $captchaAttributes = [];

    protected Repository $config;

    protected Application|Repository $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->config = $this->app['config'];
    }

    public function display(array $attributes = [], array $options = []): ?string
    {
        if (! $this->optionOrConfig($options, 'site_key')) {
            return null;
        }

        if (is_string($attributes)) {
            $attributes = [];
        }

        if (! Arr::get($options, 'lang')) {
            $options['lang'] = app()->getLocale();
        }

        $isMultiple = (bool)$this->optionOrConfig($options, 'options.multiple');
        if (! array_key_exists('id', $attributes)) {
            $attributes['id'] = $this->randomCaptchaId();
        }
        $html = '';
        if (! $isMultiple && Arr::get($attributes, 'add-js', true)) {
            $html .= '<script src="' . $this->getJsLink($options) . '" async defer></script>';
        }
        unset($attributes['add-js']);
        $attributeOptions = $this->optionOrConfig($options, 'attributes');
        if (! empty($attributeOptions)) {
            $attributes = array_merge($attributeOptions, $attributes);
        }
        if ($isMultiple) {
            $this->captchaAttributes[] = $attributes;
        } else {
            $attributes['data-sitekey'] = $this->optionOrConfig($options, 'site_key');
        }

        return $html . '<script>"use strict"; var refreshRecaptcha = function () { grecaptcha.reset(); };</script><div class="g-recaptcha" ' . $this->buildAttributes(
            $attributes
        ) . '></div>';
    }

    protected function optionOrConfig(
        array $options = [],
        string $key = '',
        string|null|array $default = null
    ): string|null|array {
        return Arr::get(
            $options,
            str_replace('options.', '', $key),
            $this->config->get('plugins.captcha.general.' . $key, $default)
        );
    }

    protected function randomCaptchaId(): string
    {
        return 'buzzNoCaptchaId_' . md5(uniqid(rand(), true));
    }

    public function getJsLink(array $options = []): string
    {
        $query = [];
        if ($this->optionOrConfig($options, 'options.multiple')) {
            $query = [
                'onload' => $this->callbackName,
                'render' => 'explicit',
            ];
        }

        $lang = $this->optionOrConfig($options, 'options.lang');

        if ($lang) {
            $query['hl'] = $lang;
        }

        return static::CAPTCHA_CLIENT_API . '?' . http_build_query($query);
    }

    protected function buildAttributes(array $attributes): string
    {
        $html = [];
        foreach ($attributes as $key => $value) {
            $html[] = $key . '="' . $value . '"';
        }

        return count($html) ? ' ' . implode(' ', $html) : '';
    }

    public function displayMultiple(array $options = []): string
    {
        if (! $this->optionOrConfig($options, 'options.multiple')) {
            return '';
        }
        $renderHtml = '';
        foreach ($this->captchaAttributes as $captchaAttribute) {
            $renderHtml .= $this->widgetIdName . '["' . $captchaAttribute['id'] . '"]=' . $this->buildCaptchaHtml(
                $captchaAttribute,
                $options
            );
        }

        return '<script type="text/javascript">var ' . $this->widgetIdName . '={};var ' . $this->callbackName . '=function(){' . $renderHtml . '};</script>';
    }

    protected function buildCaptchaHtml(array $captchaAttribute = [], array $options = []): string
    {
        $options = array_merge(
            ['sitekey' => $this->optionOrConfig($options, 'site_key')],
            $this->optionOrConfig($options, 'attributes', [])
        );
        foreach ($captchaAttribute as $key => $value) {
            $options[str_replace('data-', '', $key)] = $value;
        }
        $options = json_encode($options);

        return 'grecaptcha.render("' . $captchaAttribute['id'] . '",' . $options . ');';
    }

    public function displayJs(array $options = [], array $attributes = ['async', 'defer']): string
    {
        return '<script src="' . htmlspecialchars($this->getJsLink($options)) . '" ' . implode(
            ' ',
            $attributes
        ) . '></script>';
    }

    public function multiple(bool $multiple = true): void
    {
        $this->config->set('plugins.captcha.general.options.multiple', $multiple);
    }

    public function setOptions(array $options = []): void
    {
        $this->config->set('plugins.captcha.general.options', $options);
    }

    public function verify(string $response, string $clientIp = null, array $options = []): bool
    {
        if (empty($response)) {
            return false;
        }

        $getRequestMethod = $this->optionOrConfig($options, 'request_method');
        $requestMethod = is_string($getRequestMethod) ? $this->app->call($getRequestMethod) : null;
        $reCaptCha = new ReCaptcha($this->optionOrConfig($options, 'secret'), $requestMethod);

        return $reCaptCha->verify($response, $clientIp)->isSuccess();
    }
}
