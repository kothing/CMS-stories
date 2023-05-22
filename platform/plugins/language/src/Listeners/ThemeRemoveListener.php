<?php

namespace Botble\Language\Listeners;

use Botble\Setting\Repositories\Interfaces\SettingInterface;
use Botble\Theme\Events\ThemeRemoveEvent;
use Botble\Widget\Repositories\Interfaces\WidgetInterface;
use Exception;
use Language;

class ThemeRemoveListener
{
    protected WidgetInterface $widgetRepository;

    protected SettingInterface $settingRepository;

    public function __construct(WidgetInterface $widgetRepository, SettingInterface $settingRepository)
    {
        $this->widgetRepository = $widgetRepository;
        $this->settingRepository = $settingRepository;
    }

    public function handle(ThemeRemoveEvent $event): void
    {
        try {
            $languages = Language::getActiveLanguage(['lang_code']);

            foreach ($languages as $language) {
                $themeNameByLanguage = $event->theme . '-' . $language->lang_code;

                $this->widgetRepository->deleteBy(['theme' => $themeNameByLanguage]);

                $this->settingRepository->deleteBy(['key', 'like', 'theme-' . $themeNameByLanguage . '-%']);
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
