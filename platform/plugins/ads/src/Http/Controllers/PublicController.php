<?php

namespace Botble\Ads\Http\Controllers;

use Botble\Ads\Repositories\Interfaces\AdsInterface;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;

class PublicController extends BaseController
{
    protected AdsInterface $adsRepository;

    public function __construct(AdsInterface $adsRepository)
    {
        $this->adsRepository = $adsRepository;
    }

    public function getAdsClick(string $key, BaseHttpResponse $response)
    {
        $ads = $this->adsRepository->getFirstBy(compact('key'));

        if (! $ads || ! $ads->url) {
            return $response->setNextUrl(route('public.single'));
        }

        $ads->clicked++;
        $ads->save();

        return $response->setNextUrl($ads->url);
    }
}
