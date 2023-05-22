<?php

namespace Botble\Widget\Factories;

use Botble\Widget\AbstractWidget;
use Botble\Widget\Misc\InvalidWidgetClassException;
use Botble\Widget\Misc\ViewExpressionTrait;
use Botble\Widget\WidgetId;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

abstract class AbstractWidgetFactory
{
    use ViewExpressionTrait;

    protected AbstractWidget $widget;

    protected array $widgetConfig;

    public string $widgetName;

    public array $widgetParams;

    public array $widgetFullParams;

    public Application $app;

    public static bool $skipWidgetContainer = false;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function __call(string $widgetName, array $params = [])
    {
        array_unshift($params, $widgetName);

        return call_user_func_array([$this, 'run'], $params);
    }

    protected function instantiateWidget(array $params = []): void
    {
        WidgetId::increment();

        $this->widgetName = $this->parseFullWidgetNameFromString(array_shift($params));
        $this->widgetFullParams = $params;
        $this->widgetConfig = (array)array_shift($params);
        $this->widgetParams = $params;

        $widgetClass = $this->widgetName;

        if (! class_exists($widgetClass, false)) {
            throw new Exception($widgetClass . ' is not exists');
        }

        if (! is_subclass_of($widgetClass, AbstractWidget::class)) {
            throw new InvalidWidgetClassException('Class "' . $widgetClass . '" must extend "' . AbstractWidget::class . '" class');
        }

        $this->widget = new $widgetClass($this->widgetConfig);
    }

    protected function parseFullWidgetNameFromString(string $widgetName): string
    {
        return Str::studly(str_replace('.', '\\', $widgetName));
    }
}
