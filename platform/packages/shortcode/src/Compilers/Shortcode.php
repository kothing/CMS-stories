<?php

namespace Botble\Shortcode\Compilers;

class Shortcode
{
    protected string $name;

    protected array $attributes = [];

    public ?string $content;

    public function __construct(string $name, array $attributes = [], ?string $content = null)
    {
        $this->name = $name;
        $this->attributes = $attributes;
        $this->content = $content;
    }

    public function get(string $attribute, ?string $fallback = null): string
    {
        $value = $this->{$attribute};

        if (! empty($value)) {
            return $attribute . '="' . $value . '"';
        } elseif (! empty($fallback)) {
            return $attribute . '="' . $fallback . '"';
        }

        return '';
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function __get(string $param)
    {
        return $this->attributes[$param] ?? null;
    }
}
