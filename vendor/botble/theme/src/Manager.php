<?php

namespace Botble\Theme;

use BaseHelper;
use Illuminate\Support\Facades\File;
use Theme as ThemeFacade;

class Manager
{
    protected array $themes = [];

    public function __construct()
    {
        $this->registerTheme(self::getAllThemes());
    }

    public function registerTheme(string|array $theme): void
    {
        if (! is_array($theme)) {
            $theme = [$theme];
        }

        $this->themes = array_merge_recursive($this->themes, $theme);
    }

    public function getAllThemes(): array
    {
        $themes = [];
        $themePath = theme_path();
        foreach (BaseHelper::scanFolder($themePath) as $folder) {
            $jsonFile = $themePath . '/' . $folder . '/theme.json';

            $publicJsonFile = public_path('themes/' . ThemeFacade::getPublicThemeName() . '/theme.json');

            if (File::exists($publicJsonFile)) {
                $jsonFile = $publicJsonFile;
            }

            if (! File::exists($jsonFile)) {
                continue;
            }

            $theme = BaseHelper::getFileData($jsonFile);
            if (! empty($theme)) {
                $themes[$folder] = $theme;
            }
        }

        return $themes;
    }

    public function getThemes(): array
    {
        return $this->themes;
    }
}
