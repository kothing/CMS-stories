<?php

namespace Botble\Theme\Commands;

use Botble\Setting\Models\Setting as SettingModel;
use Botble\Theme\Commands\Traits\ThemeTrait;
use Botble\Theme\Services\ThemeService;
use Botble\Widget\Models\Widget;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:theme:rename', 'Rename theme to the new name')]
class ThemeRenameCommand extends Command
{
    use ThemeTrait;

    public function handle(File $files, ThemeService $themeService): int
    {
        $theme = $this->getTheme();

        $newName = $this->argument('newName');

        if ($theme == $newName) {
            $this->components->error('Theme name are the same!');

            return self::FAILURE;
        }

        if ($files->isDirectory(theme_path($newName))) {
            $this->components->error('Theme "' . $theme . '" is already exists.');

            return self::FAILURE;
        }

        $files->move(theme_path($theme), theme_path($newName));

        $themeService->activate($newName);

        $themeOptions = SettingModel::where('key', 'LIKE', 'theme-' . $theme . '-%')->get();

        foreach ($themeOptions as $option) {
            $option->key = str_replace('theme-' . $theme, 'theme-' . $newName, $option->key);
            $option->save();
        }

        Widget::where('theme', $theme)->update(['theme' => $newName]);

        $widgets = Widget::where('theme', 'LIKE', $theme . '-%')->get();

        foreach ($widgets as $widget) {
            $widget->theme = str_replace($theme, $newName, $widget->theme);
            $widget->save();
        }

        $this->components->info('Theme "' . $theme . '" has been renamed to ' . $newName . '!');

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The theme name that you want to rename');
        $this->addArgument('newName', InputArgument::REQUIRED, 'The new name');
    }
}
