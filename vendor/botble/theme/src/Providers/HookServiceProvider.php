<?php

namespace Botble\Theme\Providers;

use BaseHelper;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Botble\Theme\Supports\ThemeSupport;
use Botble\Theme\Supports\Vimeo;
use Botble\Theme\Supports\Youtube;
use Html;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Theme;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'addStatsWidgets'], 4, 2);

        add_filter(BASE_FILTER_AFTER_SETTING_CONTENT, [$this, 'addSetting'], 39);

        theme_option()
            ->setArgs(['debug' => app()->hasDebugModeEnabled()])
            ->setSection([
                'title' => trans('packages/theme::theme.theme_option_general'),
                'desc' => trans('packages/theme::theme.theme_option_general_description'),
                'priority' => 0,
                'id' => 'opt-text-subsection-general',
                'subsection' => true,
                'icon' => 'fa fa-home',
                'fields' => [
                    [
                        'id' => 'site_title',
                        'type' => 'text',
                        'label' => trans('core/setting::setting.general.site_title'),
                        'attributes' => [
                            'name' => 'site_title',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                                'placeholder' => trans('core/setting::setting.general.site_title'),
                                'data-counter' => 255,
                            ],
                        ],
                    ],
                    [
                        'id' => 'show_site_name',
                        'section_id' => 'opt-text-subsection-general',
                        'type' => 'customSelect',
                        'label' => trans('core/setting::setting.general.show_site_name'),
                        'attributes' => [
                            'name' => 'show_site_name',
                            'list' => [
                                '0' => 'No',
                                '1' => 'Yes',
                            ],
                            'value' => '0',
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'id' => 'seo_title',
                        'type' => 'text',
                        'label' => trans('core/setting::setting.general.seo_title'),
                        'attributes' => [
                            'name' => 'seo_title',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                                'placeholder' => trans('core/setting::setting.general.seo_title'),
                                'data-counter' => 120,
                            ],
                        ],
                    ],
                    [
                        'id' => 'seo_description',
                        'type' => 'textarea',
                        'label' => trans('core/setting::setting.general.seo_description'),
                        'attributes' => [
                            'name' => 'seo_description',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                                'rows' => 4,
                            ],
                        ],
                    ],
                    [
                        'id' => 'seo_og_image',
                        'type' => 'mediaImage',
                        'label' => trans('packages/theme::theme.theme_option_seo_open_graph_image'),
                        'attributes' => [
                            'name' => 'seo_og_image',
                            'value' => null,
                        ],
                    ],
                ],
            ])
            ->setSection([
                'title' => trans('packages/theme::theme.theme_option_logo'),
                'desc' => trans('packages/theme::theme.theme_option_logo'),
                'priority' => 0,
                'id' => 'opt-text-subsection-logo',
                'subsection' => true,
                'icon' => 'fa fa-image',
                'fields' => [
                    [
                        'id' => 'favicon',
                        'type' => 'mediaImage',
                        'label' => trans('packages/theme::theme.theme_option_favicon'),
                        'attributes' => [
                            'name' => 'favicon',
                            'value' => null,
                            'attributes' => ['allow_thumb' => false],
                        ],
                    ],
                    [
                        'id' => 'logo',
                        'type' => 'mediaImage',
                        'label' => trans('packages/theme::theme.theme_option_logo'),
                        'attributes' => [
                            'name' => 'logo',
                            'value' => null,
                            'attributes' => ['allow_thumb' => false],
                        ],
                    ],
                ],
            ]);

        add_shortcode('media', null, null, function ($shortcode) {
            $url = rtrim($shortcode->url, '/');

            if (! $url) {
                return null;
            }

            $iframe = null;

            if (Youtube::isYoutubeURL($url)) {
                $iframe = Html::tag('iframe', '', [
                    'class' => 'embed-responsive-item',
                    'allowfullscreen' => true,
                    'frameborder' => 0,
                    'height' => 315,
                    'width' => 420,
                    'src' => Youtube::getYoutubeVideoEmbedURL($url),
                ])->toHtml();
            }

            if (Vimeo::isVimeoURL($url)) {
                $videoId = Vimeo::getVimeoID($url);
                if ($videoId) {
                    $iframe = Html::tag('iframe', '', [
                        'class' => 'embed-responsive-item',
                        'height' => 315,
                        'width' => 420,
                        'allow' => 'autoplay; fullscreen; picture-in-picture',
                        'src' => 'https://player.vimeo.com/video/' . $videoId,
                    ])->toHtml();
                }
            }

            if ($iframe) {
                return Html::tag('div', $iframe, ['class' => 'embed-responsive embed-responsive-16by9 mb30'])
                    ->toHtml();
            }

            return null;
        });

        add_filter(THEME_FRONT_HEADER, function ($html) {
            $file = Theme::getStyleIntegrationPath();
            if (File::exists($file)) {
                $html .= "\n" . Html::style(Theme::asset()->url('css/style.integration.css?v=' . filectime($file)));
            }

            return $html;
        }, 15);

        if (! $this->app->environment('demo')) {
            if (config('packages.theme.general.enable_custom_html_shortcode')) {
                add_shortcode('custom-html', __('Custom HTML'), __('Add custom HTML content'), function ($shortCode) {
                    return html_entity_decode($shortCode->content);
                });

                shortcode()->setAdminConfig('custom-html', function ($attributes, $content) {
                    return view('packages/theme::shortcodes.custom-html-admin-config', compact('attributes', 'content'))
                        ->render();
                });
            }

            if (config('packages.theme.general.enable_custom_js')) {
                if (setting('custom_header_js')) {
                    add_filter(THEME_FRONT_HEADER, function ($html) {
                        return $html . ThemeSupport::getCustomJS('header');
                    }, 15);
                }

                if (setting('custom_body_js')) {
                    add_filter(THEME_FRONT_BODY, function ($html) {
                        return $html . ThemeSupport::getCustomJS('body');
                    }, 15);
                }

                if (setting('custom_footer_js')) {
                    add_filter(THEME_FRONT_FOOTER, function ($html) {
                        return $html . ThemeSupport::getCustomJS('footer');
                    }, 15);
                }
            }

            if (config('packages.theme.general.enable_custom_html')) {
                if (setting('custom_header_html')) {
                    add_filter(THEME_FRONT_HEADER, function ($html) {
                        return $html . ThemeSupport::getCustomHtml('header');
                    }, 16);
                }

                if (setting('custom_body_html')) {
                    add_filter(THEME_FRONT_BODY, function ($html) {
                        return $html . ThemeSupport::getCustomHtml('body');
                    }, 16);
                }

                if (setting('custom_footer_html')) {
                    add_filter(THEME_FRONT_FOOTER, function ($html) {
                        return $html . ThemeSupport::getCustomHtml('footer');
                    }, 16);
                }
            }

            add_filter(THEME_FRONT_FOOTER, function ($html) {
                if (! auth()->check() || ! admin_bar()->isDisplay() || ! (int)setting('show_admin_bar', 1)) {
                    return $html;
                }

                return $html . Html::style('vendor/core/packages/theme/css/admin-bar.css') . admin_bar()->render();
            }, 14);
        }
    }

    public function addStatsWidgets(array $widgets, Collection $widgetSettings): array
    {
        $themes = count(BaseHelper::scanFolder(theme_path()));

        return (new DashboardWidgetInstance())
            ->setType('stats')
            ->setPermission('theme.index')
            ->setTitle(trans('packages/theme::theme.theme'))
            ->setKey('widget_total_themes')
            ->setIcon('fa fa-paint-brush')
            ->setColor('#e7505a')
            ->setStatsTotal($themes)
            ->setRoute(route('theme.index'))
            ->init($widgets, $widgetSettings);
    }

    public function addSetting(?string $data = null): string
    {
        return $data . view('packages/theme::setting')->render();
    }
}
