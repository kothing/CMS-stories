<?php

namespace Botble\Widget;

use Botble\Widget\Repositories\Interfaces\WidgetInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Language;
use Theme;

class WidgetGroupCollection
{
    protected array $groups;

    protected Application $app;

    protected Collection|array $data = [];

    protected bool $loaded = false;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function group(string $sidebarId): WidgetGroup
    {
        if (isset($this->groups[$sidebarId])) {
            return $this->groups[$sidebarId];
        }
        $this->groups[$sidebarId] = new WidgetGroup(['id' => $sidebarId, 'name' => $sidebarId], $this->app);

        return $this->groups[$sidebarId];
    }

    public function setGroup(array $args): WidgetGroupCollection
    {
        if (isset($this->groups[$args['id']])) {
            $group = $this->groups[$args['id']];
            $group->setName(Arr::get($args, 'name'));
            $group->setDescription(Arr::get($args, 'description'));
            $this->groups[$args['id']] = $group;
        } else {
            $this->groups[$args['id']] = new WidgetGroup($args, $this->app);
        }

        return $this;
    }

    public function removeGroup(string $groupId): WidgetGroupCollection
    {
        if (isset($this->groups[$groupId])) {
            unset($this->groups[$groupId]);
        }

        return $this;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function render(string $sidebarId): string
    {
        $this->load();

        foreach ($this->data as $widget) {
            $this->group($widget->sidebar_id)
                ->position($widget->position)
                ->addWidget($widget->widget_id, $widget->data);
        }

        return $this->group($sidebarId)->display();
    }

    public function load(bool $force = false): void
    {
        if (! $this->loaded || $force) {
            $this->data = $this->read();
            $this->loaded = true;
        }
    }

    protected function read(): Collection
    {
        $languageCode = null;
        if (is_plugin_active('language')) {
            $currentLocale = is_in_admin() ? Language::getCurrentAdminLocaleCode() : Language::getCurrentLocaleCode();
            $languageCode = $currentLocale && $currentLocale != Language::getDefaultLocaleCode() ? '-' . $currentLocale : null;
        }

        return app(WidgetInterface::class)->allBy(['theme' => Theme::getThemeName() . $languageCode]);
    }

    public function getData(): Collection
    {
        return $this->data;
    }
}
