<?php

namespace Botble\Ads\Supports;

use Botble\Ads\Repositories\Interfaces\AdsInterface;
use Botble\Base\Enums\BaseStatusEnum;
use Carbon\Carbon;
use Html;
use Illuminate\Support\Collection;
use RvMedia;

class AdsManager
{
    protected Collection|array $data = [];

    protected bool $loaded = false;

    protected array $locations = [];

    public function __construct()
    {
        $this->locations = [
            'not_set' => trans('plugins/ads::ads.not_set'),
        ];
    }

    public function display(string $location, array $attributes = []): string
    {
        $this->load();

        $data = $this->data
            ->where('location', $location)
            ->sortBy('order');

        if ($data->count() > 1) {
            $data = $data->random(1);
        }

        $html = '';
        foreach ($data as $item) {
            if (! $item->image) {
                continue;
            }

            $image = Html::image(RvMedia::getImageUrl($item->image), $item->name, ['style' => 'max-width: 100%'])
                ->toHtml();

            if ($item->url) {
                $image = Html::link(route('public.ads-click', $item->key), $image, ['target' => '_blank'], null, false)
                    ->toHtml();
            }

            $html .= Html::tag('div', $image, $attributes)->toHtml();
        }

        return $html;
    }

    public function load(bool $force = false): self
    {
        if (! $this->loaded || $force) {
            $this->data = $this->read();
            $this->loaded = true;
        }

        return $this;
    }

    protected function read(): Collection
    {
        return app(AdsInterface::class)->getAll();
    }

    public function locationHasAds(string $location): bool
    {
        $this->load();

        return (bool)$this->data
            ->where('location', $location)
            ->sortBy('order')
            ->count();
    }

    public function displayAds(?string $key, array $attributes = [], array $linkAttributes = []): ?string
    {
        if (! $key) {
            return null;
        }

        $this->load();

        $ads = $this->data
            ->where('key', $key)
            ->first();

        if (! $ads || ! $ads->image) {
            return null;
        }

        $image = Html::image(RvMedia::getImageUrl($ads->image), $ads->name, ['style' => 'max-width: 100%'])->toHtml();

        if ($ads->url) {
            $image = Html::link(route('public.ads-click', $ads->key), $image, $linkAttributes + ['target' => '_blank'], null, false)
                ->toHtml();
        }

        return Html::tag('div', $image, $attributes)->toHtml();
    }

    public function getData(bool $isLoad = false, bool $isNotExpired = false): Collection
    {
        if ($isLoad) {
            $this->load();
        }

        if ($isNotExpired) {
            return $this->data
                ->where('status', BaseStatusEnum::PUBLISHED)
                ->filter(function ($item) {
                    if ($expiredAt = $item->getRawOriginal('expired_at')) {
                        return Carbon::parse($expiredAt)->gte(now());
                    }

                    return true;
                });
        }

        return $this->data;
    }

    public function registerLocation(string $key, string $name): self
    {
        $this->locations[$key] = $name;

        return $this;
    }

    public function getLocations(): array
    {
        return $this->locations;
    }
}
