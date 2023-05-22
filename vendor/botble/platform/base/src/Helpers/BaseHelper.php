<?php

namespace Botble\Base\Helpers;

use Carbon\Carbon;
use Exception;
use Html;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;

class BaseHelper
{
    public function formatTime(Carbon $timestamp, ?string $format = 'j M Y H:i'): string
    {
        $first = Carbon::create(0000, 0, 0, 00, 00, 00);

        if ($timestamp->lte($first)) {
            return '';
        }

        return $timestamp->format($format);
    }

    public function formatDate(?string $date, ?string $format = null): ?string
    {
        if (empty($format)) {
            $format = config('core.base.general.date_format.date');
        }

        if (empty($date)) {
            return $date;
        }

        return $this->formatTime(Carbon::parse($date), $format);
    }

    public function formatDateTime(?string $date, string $format = null): ?string
    {
        if (empty($format)) {
            $format = config('core.base.general.date_format.date_time');
        }

        if (empty($date)) {
            return $date;
        }

        return $this->formatTime(Carbon::parse($date), $format);
    }

    public function humanFilesize(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'kB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return number_format($bytes, $precision, ',', '.') . ' ' . $units[$pow];
    }

    public function getFileData(string $file, bool $convertToArray = true)
    {
        $file = File::get($file);
        if (! empty($file)) {
            if ($convertToArray) {
                return json_decode($file, true);
            }

            return $file;
        }

        if (! $convertToArray) {
            return null;
        }

        return [];
    }

    public function saveFileData(string $path, array|string|null $data, bool $json = true): bool
    {
        try {
            if ($json) {
                $data = $this->jsonEncodePrettify($data);
            }

            if (! File::isDirectory(File::dirname($path))) {
                File::makeDirectory(File::dirname($path), 493, true);
            }

            File::put($path, $data);

            return true;
        } catch (Exception $exception) {
            info($exception->getMessage());

            return false;
        }
    }

    public function jsonEncodePrettify(array|string|null $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function scanFolder(string $path, array $ignoreFiles = []): array
    {
        if (File::isDirectory($path)) {
            $data = array_diff(scandir($path), array_merge(['.', '..', '.DS_Store'], $ignoreFiles));
            natsort($data);

            return $data;
        }

        return [];
    }

    public function getAdminPrefix(): string
    {
        return config('core.base.general.admin_dir');
    }

    public function getAdminMasterLayoutTemplate(): string
    {
        return apply_filters('base_filter_admin_master_layout_template', 'core/base::layouts.master');
    }

    public function siteLanguageDirection(): string
    {
        return apply_filters(BASE_FILTER_SITE_LANGUAGE_DIRECTION, setting('locale_direction', 'ltr'));
    }

    public function adminLanguageDirection(): string
    {
        $direction = session('admin_locale_direction', setting('admin_locale_direction', 'ltr'));

        return apply_filters(BASE_FILTER_ADMIN_LANGUAGE_DIRECTION, $direction);
    }

    public function isHomepage(?int $pageId = null): bool
    {
        $homepageId = $this->getHomepageId();

        return $pageId && $homepageId && $pageId == $homepageId;
    }

    public function getHomepageId(): ?string
    {
        return theme_option('homepage_id', setting('show_on_front'));
    }

    /**
     * @param Builder|\Illuminate\Database\Eloquent\Builder $query
     */
    public function isJoined($query, string $table): bool
    {
        $joins = $query->getQuery()->joins;

        if ($joins == null) {
            return false;
        }

        foreach ($joins as $join) {
            if ($join->table == $table) {
                return true;
            }
        }

        return false;
    }

    public function availableRichEditors(): array
    {
        return apply_filters(BASE_FILTER_AVAILABLE_EDITORS, [
            'ckeditor' => 'CKEditor',
            'tinymce' => 'TinyMCE',
        ]);
    }

    public function getRichEditor(): string
    {
        $richEditor = setting('rich_editor', config('core.base.general.editor.primary'));

        if (array_key_exists($richEditor, $this->availableRichEditors())) {
            return $richEditor;
        }

        setting()->set(['rich_editor' => 'ckeditor'])->save();

        return 'ckeditor';
    }

    public function removeQueryStringVars(?string $url, array|string $key): ?string
    {
        if (! is_array($key)) {
            $key = [$key];
        }

        foreach ($key as $item) {
            $url = preg_replace('/(.*)(?|&)' . $item . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
            $url = substr($url, 0, -1);
        }

        return $url;
    }

    public function cleanEditorContent(?string $value): string
    {
        $value = str_replace('<span class="style-scope yt-formatted-string" dir="auto">', '', $value);

        return htmlentities($this->clean($value));
    }

    public function getPhoneValidationRule(): string
    {
        return config('core.base.general.phone_validation_rule');
    }

    public function sortSearchResults(array|Collection $collection, string $searchTerms, string $column): Collection
    {
        if (! $collection instanceof Collection) {
            $collection = collect($collection);
        }

        return $collection->sortByDesc(function ($item) use ($searchTerms, $column) {
            $searchTerms = explode(' ', $searchTerms);

            // The bigger the weight, the higher the record
            $weight = 0;

            // Iterate through search terms
            foreach ($searchTerms as $term) {
                if (str_contains($item->{$column}, $term)) {
                    // Increase weight if the search term is found
                    $weight += 1;
                }
            }

            return $weight;
        });
    }

    public function getDateFormats(): array
    {
        $formats = [
            'Y-m-d',
            'Y-M-d',
            'y-m-d',
            'm-d-Y',
            'M-d-Y',
        ];

        foreach ($formats as $format) {
            $formats[] = str_replace('-', '/', $format);
        }

        $formats[] = 'M d, Y';

        return $formats;
    }

    public function clean(array|string|null $dirty, array|string $config = null): ?string
    {
        if (config('core.base.general.enable_less_secure_web', false)) {
            return $dirty;
        }

        return clean($dirty ?: '', $config);
    }

    public function html(array|string|null $dirty, array|string $config = null): HtmlString
    {
        return new HtmlString($this->clean($dirty, $config));
    }

    public function hexToRgba(string $color, float $opacity = 1): string
    {
        $rgb = implode(',', $this->hexToRgb($color));

        if ($opacity == 1) {
            return 'rgb(' . $rgb . ')';
        }

        return 'rgba(' . $rgb . ', ' . $opacity . ')';
    }

    public function hexToRgb(string $color): array
    {
        [$red, $green, $blue] = sscanf($color, '#%02x%02x%02x');

        $blue = $blue === null ? 0 : $blue;

        return compact('red', 'green', 'blue');
    }

    public function iniSet(string $key, int|string|null $value): self
    {
        if (config('core.base.general.enable_ini_set', true)) {
            @ini_set($key, $value);
        }

        return $this;
    }

    public function maximumExecutionTimeAndMemoryLimit(): self
    {
        $this->iniSet('max_execution_time', -1);
        $this->iniSet('memory_limit', -1);

        return $this;
    }

    public function removeSpecialCharacters(?string $string): array|string|null
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }

    public function getInputValueFromQueryString(string $name): string
    {
        $value = request()->input($name);

        if (! is_string($value)) {
            return '';
        }

        return $value;
    }

    public function cleanShortcodes(?string $content): ?string
    {
        if (! $content) {
            return $content;
        }

        $content = $this->clean($content);

        $shortcodeCompiler = shortcode()->getCompiler();

        return $shortcodeCompiler->strip($content);
    }

    public function stringify($content): ?string
    {
        if (empty($content)) {
            return null;
        }

        if (is_string($content) || is_numeric($content) || is_bool($content)) {
            return $content;
        }

        if (is_array($content)) {
            return json_encode($content);
        }

        return null;
    }

    public function getGoogleFontsURL(): string
    {
        return config('core.base.general.google_fonts_url', 'https://fonts.bunny.net');
    }

    public function googleFonts(string $font, bool $inline = true)
    {
        if (! config('core.base.general.google_fonts_enabled_cache')) {
            $font = str_replace('https://fonts.googleapis.com', $this->getGoogleFontsURL(), $font);

            return Html::style($font);
        }

        $font = str_replace($this->getGoogleFontsURL(), 'https://fonts.googleapis.com', $font);

        $font = app('core:google-fonts')->load($font);

        if (! $inline) {
            return $font->link();
        }

        return $font->toHtml();
    }
}
