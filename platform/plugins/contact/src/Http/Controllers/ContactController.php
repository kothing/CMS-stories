<?php

namespace Botble\Contact\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Traits\HasDeleteManyItemsTrait;
use Botble\Contact\Enums\ContactStatusEnum;
use Botble\Contact\Forms\ContactForm;
use Botble\Contact\Http\Requests\ContactReplyRequest;
use Botble\Contact\Http\Requests\EditContactRequest;
use Botble\Contact\Repositories\Interfaces\ContactReplyInterface;
use Botble\Contact\Tables\ContactTable;
use Botble\Contact\Repositories\Interfaces\ContactInterface;
use EmailHandler;
use Exception;
use Illuminate\Http\Request;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;

class ContactController extends BaseController
{
    use HasDeleteManyItemsTrait;

    protected ContactInterface $contactRepository;

    public function __construct(ContactInterface $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function index(ContactTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/contact::contact.menu'));

        return $dataTable->renderTable();
    }

    public function edit(int $id, FormBuilder $formBuilder, Request $request)
    {
        page_title()->setTitle(trans('plugins/contact::contact.edit'));

        $contact = $this->contactRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $contact));

        return $formBuilder->create(ContactForm::class, ['model' => $contact])->renderForm();
    }

    public function update(int $id, EditContactRequest $request, BaseHttpResponse $response)
    {
        $contact = $this->contactRepository->findOrFail($id);

        $contact->fill($request->input());

        $this->contactRepository->createOrUpdate($contact);

        event(new UpdatedContentEvent(CONTACT_MODULE_SCREEN_NAME, $request, $contact));

        return $response
            ->setPreviousUrl(route('contacts.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(int $id, Request $request, BaseHttpResponse $response)
    {
        try {
            $contact = $this->contactRepository->findOrFail($id);
            $this->contactRepository->delete($contact);
            event(new DeletedContentEvent(CONTACT_MODULE_SCREEN_NAME, $request, $contact));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deletes(Request $request, BaseHttpResponse $response)
    {
        return $this->executeDeleteItems($request, $response, $this->contactRepository, CONTACT_MODULE_SCREEN_NAME);
    }

    public function postReply(
        int $id,
        ContactReplyRequest $request,
        BaseHttpResponse $response,
        ContactReplyInterface $contactReplyRepository
    ) {
        $contact = $this->contactRepository->findOrFail($id);

        EmailHandler::send($request->input('message'), 'Re: ' . $contact->subject, $contact->email);

        $contactReplyRepository->create([
            'message' => $request->input('message'),
            'contact_id' => $id,
        ]);

        $contact->status = ContactStatusEnum::READ();
        $this->contactRepository->createOrUpdate($contact);

        return $response
            ->setMessage(trans('plugins/contact::contact.message_sent_success'));
    }
}
