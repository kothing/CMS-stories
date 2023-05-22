<?php

use Botble\Page\Repositories\Interfaces\PageInterface;
use Botble\Page\Supports\Template;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

if (! function_exists('get_page_by_slug')) {
    function get_page_by_slug(string $slug): ?Model
    {
        return app(PageInterface::class)->getBySlug($slug, true);
    }
}

if (! function_exists('get_all_pages')) {
    function get_all_pages(bool $active = true): Collection
    {
        return app(PageInterface::class)->getAllPages($active);
    }
}

if (! function_exists('register_page_template')) {
    function register_page_template(array $templates): void
    {
        Template::registerPageTemplate($templates);
    }
}

if (! function_exists('get_page_templates')) {
    function get_page_templates(): array
    {
        return Template::getPageTemplates();
    }
}
