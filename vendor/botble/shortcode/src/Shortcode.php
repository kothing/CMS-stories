<?php

namespace Botble\Shortcode;

use Botble\Shortcode\Compilers\ShortcodeCompiler;
use Illuminate\Support\HtmlString;

class Shortcode
{
    protected ShortcodeCompiler $compiler;

    public function __construct(ShortcodeCompiler $compiler)
    {
        $this->compiler = $compiler;
    }

    public function register(string $key, ?string $name, ?string $description = null, $callback = null, string $previewImage = ''): Shortcode
    {
        $this->compiler->add($key, $name, $description, $callback, $previewImage);

        return $this;
    }

    public function enable(): Shortcode
    {
        $this->compiler->enable();

        return $this;
    }

    public function disable(): Shortcode
    {
        $this->compiler->disable();

        return $this;
    }

    public function compile(string $value, bool $force = false): HtmlString
    {
        $html = $this->compiler->compile($value, $force);

        return new HtmlString($html);
    }

    public function strip(?string $value): ?string
    {
        return $this->compiler->strip($value);
    }

    public function getAll(): array
    {
        return $this->compiler->getRegistered();
    }

    public function setAdminConfig(string $key, string|null|callable|array $html): void
    {
        $this->compiler->setAdminConfig($key, $html);
    }

    public function generateShortcode(string $name, array $attributes = []): string
    {
        $parsedAttributes = '';
        foreach ($attributes as $key => $attribute) {
            $parsedAttributes .= ' ' . $key . '="' . $attribute . '"';
        }

        return '[' . $name . $parsedAttributes . '][/' . $name . ']';
    }

    public function getCompiler(): ShortcodeCompiler
    {
        return $this->compiler;
    }
}
