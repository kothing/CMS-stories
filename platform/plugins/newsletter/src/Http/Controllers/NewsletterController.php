<?php

namespace Botble\Newsletter\Http\Controllers;

use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Traits\HasDeleteManyItemsTrait;
use Botble\Newsletter\Repositories\Interfaces\NewsletterInterface;
use Botble\Newsletter\Tables\NewsletterTable;
use Exception;
use Illuminate\Http\Request;

class NewsletterController extends BaseController
{
    use HasDeleteManyItemsTrait;

    protected NewsletterInterface $newsletterRepository;

    public function __construct(NewsletterInterface $newsletterRepository)
    {
        $this->newsletterRepository = $newsletterRepository;
    }

    public function index(NewsletterTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/newsletter::newsletter.name'));

        return $dataTable->renderTable();
    }

    public function destroy(Request $request, int $id, BaseHttpResponse $response)
    {
        try {
            $newsletter = $this->newsletterRepository->findOrFail($id);
            $this->newsletterRepository->delete($newsletter);

            event(new DeletedContentEvent(NEWSLETTER_MODULE_SCREEN_NAME, $request, $newsletter));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        return $this->executeDeleteItems(
            $request,
            $response,
            $this->newsletterRepository,
            NEWSLETTER_MODULE_SCREEN_NAME
        );
    }
}
