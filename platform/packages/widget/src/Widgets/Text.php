<?php

namespace Botble\Widget\Widgets;

use Botble\Widget\AbstractWidget;

class Text extends AbstractWidget
{
    protected $frontendTemplate = 'packages/widget::widgets.text.frontend';

    protected $backendTemplate = 'packages/widget::widgets.text.backend';

    protected $isCore = true;

    public function __construct()
    {
        parent::__construct([
            'name' => trans('packages/widget::widget.widget_text'),
            'description' => trans('packages/widget::widget.widget_text_description'),
            'content' => null,
        ]);
    }
}
