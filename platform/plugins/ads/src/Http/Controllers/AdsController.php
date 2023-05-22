<?php

namespace Botble\Ads\Http\Controllers;

use Botble\Ads\Forms\AdsForm;
use Botble\Ads\Http\Requests\AdsRequest;
use Botble\Ads\Repositories\Interfaces\AdsInterface;
use Botble\Ads\Tables\AdsTable;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Exception;
use Illuminate\Http\Request;

class AdsController extends BaseController
{
    protected AdsInterface $adsRepository;

    public function __construct(AdsInterface $adsRepository)
    {
        $this->adsRepository = $adsRepository;
    }

    public function index(AdsTable $table)
    {
        page_title()->setTitle(trans('plugins/ads::ads.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/ads::ads.create'));

        return $formBuilder->create(AdsForm::class)->renderForm();
    }

    public function store(AdsRequest $request, BaseHttpResponse $response)
    {
        $ads = $this->adsRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $ads));

        return $response
            ->setPreviousUrl(route('ads.index'))
            ->setNextUrl(route('ads.edit', $ads->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(int $id, FormBuilder $formBuilder, Request $request)
    {
        $ads = $this->adsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $ads));

        page_title()->setTitle(trans('plugins/ads::ads.edit') . ' "' . $ads->name . '"');

        return $formBuilder->create(AdsForm::class, ['model' => $ads])->renderForm();
    }

    public function update(int $id, AdsRequest $request, BaseHttpResponse $response)
    {
        $ads = $this->adsRepository->findOrFail($id);

        $ads->fill($request->input());

        $this->adsRepository->createOrUpdate($ads);

        event(new UpdatedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $ads));

        return $response
            ->setPreviousUrl(route('ads.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Request $request, int $id, BaseHttpResponse $response)
    {
        try {
            $ads = $this->adsRepository->findOrFail($id);

            $this->adsRepository->delete($ads);

            event(new DeletedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $ads));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $ads = $this->adsRepository->findOrFail($id);
            $this->adsRepository->delete($ads);
            event(new DeletedContentEvent(ADS_MODULE_SCREEN_NAME, $request, $ads));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
