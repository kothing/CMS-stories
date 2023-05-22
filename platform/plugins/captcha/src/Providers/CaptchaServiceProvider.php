<?php

namespace Botble\Captcha\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Captcha\Captcha;
use Botble\Captcha\CaptchaV3;
use Botble\Captcha\Facades\CaptchaFacade;
use Botble\Captcha\MathCaptcha;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Theme;

class CaptchaServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    protected bool $defer = false;

    public function register(): void
    {
        config([
            'plugins.captcha.general.secret' => setting('captcha_secret'),
            'plugins.captcha.general.site_key' => setting('captcha_site_key'),
            'plugins.captcha.general.type' => setting('captcha_type'),
        ]);

        $this->app->singleton('captcha', function ($app) {
            if (config('plugins.captcha.general.type') == 'v3') {
                return new CaptchaV3($app);
            }

            return new Captcha($app);
        });

        $this->app->singleton('math-captcha', function ($app) {
            return new MathCaptcha($app['session']);
        });

        AliasLoader::getInstance()->alias('Captcha', CaptchaFacade::class);
    }

    public function boot(): void
    {
        $this->setNamespace('plugins/captcha')
            ->loadAndPublishConfigurations(['general'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations();

        $this->bootValidator();

        if (defined('THEME_MODULE_SCREEN_NAME') && setting('captcha_hide_badge')) {
            Theme::asset()->writeStyle('hide-recaptcha-badge', '.grecaptcha-badge { visibility: hidden; }');
        }

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }

    /**
     * Create captcha validator rule
     */
    public function bootValidator()
    {
        $app = $this->app;

        /**
         * @var Validator $validator
         */
        $validator = $app['validator'];
        $validator->extend('captcha', function ($attribute, $value, $parameters) use ($app) {
            /**
             * @var Captcha $captcha
             */
            $captcha = $app['captcha'];
            /**
             * @var Request $request
             */
            $request = $app['request'];

            if (config('plugins.captcha.general.type') == 'v3') {
                if (empty($parameters)) {
                    $parameters = ['form', '0.6'];
                }
            } else {
                $parameters = $this->mapParameterToOptions($parameters);
            }

            return $captcha->verify($value, $request->getClientIp(), $parameters);
        });

        $validator->replacer('captcha', function ($message) {
            return $message === 'validation.captcha' ? trans('plugins/captcha::captcha.failed_validate') : $message;
        });

        if ($app->bound('form')) {
            $app['form']->macro('captcha', function ($attributes = []) use ($app) {
                return $app['captcha']->display($attributes, ['lang' => $app->getLocale()]);
            });
        }

        $validator->extend('math_captcha', function ($attribute, $value) {
            return $this->app['math-captcha']->verify((string)$value);
        });
    }

    public function mapParameterToOptions(array $parameters = []): array
    {
        if (! is_array($parameters)) {
            return [];
        }

        $options = [];

        foreach ($parameters as $parameter) {
            $option = explode(':', $parameter);
            if (count($option) === 2) {
                Arr::set($options, $option[0], $option[1]);
            }
        }

        return $options;
    }

    public function provides(): array
    {
        return ['captcha', 'math-captcha'];
    }
}
