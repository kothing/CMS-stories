<?php

namespace Botble\Slug\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Supports\SettingStore;
use Botble\Slug\Http\Requests\SlugRequest;
use Botble\Slug\Http\Requests\SlugSettingsRequest;
use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Botble\Slug\Services\SlugService;
use Illuminate\Support\Str;
use Menu;

class SlugController extends BaseController
{
    protected SlugInterface $slugRepository;

    protected SlugService $slugService;

    public function __construct(SlugInterface $slugRepository, SlugService $slugService)
    {
        $this->slugRepository = $slugRepository;
        $this->slugService = $slugService;
    }

    public function store(SlugRequest $request)
    {
        return $this->slugService->create(
            $request->input('value'),
            $request->input('slug_id'),
            $request->input('model')
        );
    }

    public function getSettings()
    {
        page_title()->setTitle(trans('packages/slug::slug.settings.title'));

        return view('packages/slug::settings');
    }

    public function postSettings(SlugSettingsRequest $request, BaseHttpResponse $response, SettingStore $settingStore)
    {
        foreach ($request->except(['_token']) as $settingKey => $settingValue) {
            if (Str::contains($settingKey, '-model-key')) {
                continue;
            }

            if ($settingStore->get($settingKey) !== (string)$settingValue) {
                $this->slugRepository->update(
                    ['reference_type' => $request->input($settingKey . '-model-key')],
                    ['prefix' => (string)$settingValue]
                );

                Menu::clearCacheMenuItems();
            }

            $settingStore->set($settingKey, (string)$settingValue);
        }

        $settingStore->save();

        return $response
            ->setPreviousUrl(route('slug.settings'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }
}
