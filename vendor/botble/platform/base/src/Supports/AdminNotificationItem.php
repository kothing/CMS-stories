<?php

namespace Botble\Base\Supports;

use Closure;

class AdminNotificationItem
{
    protected string $title = '';

    protected string $description = '';

    protected string $label = '';

    protected Closure|string|null $route = null;

    protected array $action = [];

    public static function make(): static
    {
        return new static();
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function action(string $label, Closure|string|null $route): self
    {
        $this->route = $route;
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRoute(): Closure|string|null
    {
        return $this->route;
    }
}
