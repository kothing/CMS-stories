<?php

namespace Botble\Theme;

use Illuminate\Support\Facades\URL;

class Breadcrumb
{
    public array $crumbs = [];

    public function add(string|array $label, ?string $url = ''): self
    {
        if (is_array($label)) {
            if (count($label) > 0) {
                foreach ($label as $crumb) {
                    $defaults = [
                        'label' => '',
                        'url' => '',
                    ];
                    $crumb = array_merge($defaults, $crumb);
                    $this->add($crumb['label'], $crumb['url']);
                }
            }
        } else {
            $label = trim(strip_tags($label, '<i><b><strong>'));
            if (! preg_match('|^http(s)?|', $url)) {
                $url = URL::to($url);
            }

            $this->crumbs[] = ['label' => $label, 'url' => $url];
        }

        return $this;
    }

    public function render(): string
    {
        return view('packages/theme::partials.breadcrumb')->render();
    }

    public function getCrumbs(): array
    {
        return $this->crumbs;
    }
}
