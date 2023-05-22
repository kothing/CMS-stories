<?php

use Botble\Shortcode\Shortcode;
use Illuminate\Support\HtmlString;

if (! function_exists('shortcode')) {
    function shortcode(): Shortcode
    {
        return app('shortcode');
    }
}

if (! function_exists('add_shortcode')) {
    function add_shortcode(string $key, ?string $name, ?string $description = null, string|null|callable|array $callback = null, ?string $previewImage = ''): Shortcode
    {
        return shortcode()->register($key, $name, $description, $callback, $previewImage);
    }
}

if (! function_exists('do_shortcode')) {
    function do_shortcode(string $content): HtmlString
    {
        return shortcode()->compile($content, true);
    }
}

if (! function_exists('generate_shortcode')) {
    function generate_shortcode(string $name, array $attributes = []): string
    {
        return shortcode()->generateShortcode($name, $attributes);
    }
}
