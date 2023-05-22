<?php

namespace Botble\ACL\Http\Controllers;

use Assets;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Media\Services\ThumbnailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Botble\ACL\Forms\PasswordForm;
use Botble\ACL\Forms\ProfileForm;
use Botble\ACL\Forms\UserForm;
use Botble\ACL\Tables\UserTable;
use Botble\ACL\Http\Requests\CreateUserRequest;
use Botble\ACL\Http\Requests\UpdatePasswordRequest;
use Botble\ACL\Http\Requests\UpdateProfileRequest;
use Botble\ACL\Models\UserMeta;
use Botble\ACL\Repositories\Interfaces\RoleInterface;
use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\ACL\Services\ChangePasswordService;
use Botble\ACL\Services\CreateUserService;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Media\Repositories\Interfaces\MediaFileInterface;
use Botble\ACL\Http\Requests\AvatarRequest;
use Exception;
use Illuminate\Http\Request;
use RvMedia;

class UserController extends BaseController
{
    protected UserInterface $userRepository;

    protected RoleInterface $roleRepository;

    protected MediaFileInterface $fileRepository;

    public function __construct(
        UserInterface $userRepository,
        RoleInterface $roleRepository,
        MediaFileInterface $fileRepository
    ) {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->fileRepository = $fileRepository;
    }

    public function index(UserTable $dataTable)
    {
        page_title()->setTitle(trans('core/acl::users.users'));

        Assets::addScripts(['bootstrap-editable', 'jquery-ui'])
            ->addStyles(['bootstrap-editable']);

        return $dataTable->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('core/acl::users.create_new_user'));

        return $formBuilder->create(UserForm::class)->renderForm();
    }

    public function store(CreateUserRequest $request, CreateUserService $service, BaseHttpResponse $response)
    {
        $user = $service->execute($request);

        event(new CreatedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));

        return $response
            ->setPreviousUrl(route('users.index'))
            ->setNextUrl(route('users.profile.view', $user->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function destroy(int $id, Request $request, BaseHttpResponse $response)
    {
        if ($request->user()->getKey() == $id) {
            return $response
                ->setError()
                ->setMessage(trans('core/acl::users.delete_user_logged_in'));
        }

        try {
            $user = $this->userRepository->findOrFail($id);

            if (! $request->user()->isSuperUser() && $user->isSuperUser()) {
                return $response
                    ->setError()
                    ->setMessage(trans('core/acl::users.cannot_delete_super_user'));
            }

            $this->userRepository->delete($user);
            event(new DeletedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));

            return $response->setMessage(trans('core/acl::users.deleted'));
        } catch (Exception) {
            return $response
                ->setError()
                ->setMessage(trans('core/acl::users.cannot_delete'));
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
            if ($request->user()->getKey() == $id) {
                return $response
                    ->setError()
                    ->setMessage(trans('core/acl::users.delete_user_logged_in'));
            }

            try {
                $user = $this->userRepository->findOrFail($id);
                if (! $request->user()->isSuperUser() && $user->isSuperUser()) {
                    continue;
                }
                $this->userRepository->delete($user);
                event(new DeletedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));
            } catch (Exception $exception) {
                return $response
                    ->setError()
                    ->setMessage($exception->getMessage());
            }
        }

        return $response->setMessage(trans('core/acl::users.deleted'));
    }

    public function getUserProfile(int $id, Request $request, FormBuilder $formBuilder)
    {
        Assets::addScripts(['bootstrap-pwstrength', 'cropper'])
            ->addScriptsDirectly('vendor/core/core/acl/js/profile.js');

        $user = $this->userRepository->findOrFail($id);

        page_title()->setTitle(trans(':name', ['name' => $user->name]));

        $form = $formBuilder
            ->create(ProfileForm::class, ['model' => $user])
            ->setUrl(route('users.update-profile', $user->id));

        $passwordForm = $formBuilder
            ->create(PasswordForm::class)
            ->setUrl(route('users.change-password', $user->id));

        $canChangeProfile = $request->user()->getKey() == $id || $request->user()->isSuperUser();

        if (! $canChangeProfile) {
            $form->disableFields();
            $form->removeActionButtons();
            $form->setActionButtons(' ');
            $passwordForm->disableFields();
            $passwordForm->removeActionButtons();
            $passwordForm->setActionButtons(' ');
        }

        if ($request->user()->isSuperUser()) {
            $passwordForm->remove('old_password');
        }
        $form = $form->renderForm();
        $passwordForm = $passwordForm->renderForm();

        return view('core/acl::users.profile.base', compact('user', 'form', 'passwordForm', 'canChangeProfile'));
    }

    public function postUpdateProfile(int $id, UpdateProfileRequest $request, BaseHttpResponse $response)
    {
        $user = $this->userRepository->findOrFail($id);

        $currentUser = $request->user();
        if (($currentUser->hasPermission('users.update-profile') && $currentUser->getKey() === $user->id) ||
            $currentUser->isSuperUser()
        ) {
            if ($user->email !== $request->input('email')) {
                $users = $this->userRepository
                    ->getModel()
                    ->where('email', $request->input('email'))
                    ->where('id', '<>', $user->id)
                    ->exists();

                if ($users) {
                    return $response
                        ->setError()
                        ->setMessage(trans('core/acl::users.email_exist'))
                        ->withInput();
                }
            }

            if ($user->username !== $request->input('username')) {
                $users = $this->userRepository
                    ->getModel()
                    ->where('username', $request->input('username'))
                    ->where('id', '<>', $user->id)
                    ->exists();

                if ($users) {
                    return $response
                        ->setError()
                        ->setMessage(trans('core/acl::users.username_exist'))
                        ->withInput();
                }
            }
        }

        $user->fill($request->input());
        $this->userRepository->createOrUpdate($user);
        do_action(USER_ACTION_AFTER_UPDATE_PROFILE, USER_MODULE_SCREEN_NAME, $request, $user);

        event(new UpdatedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));

        return $response->setMessage(trans('core/acl::users.update_profile_success'));
    }

    public function postChangePassword(
        int $id,
        UpdatePasswordRequest $request,
        ChangePasswordService $service,
        BaseHttpResponse $response
    ) {
        $request->merge(['id' => $id]);
        $result = $service->execute($request);

        if ($result instanceof Exception) {
            return $response
                ->setError()
                ->setMessage($result->getMessage());
        }

        return $response->setMessage(trans('core/acl::users.password_update_success'));
    }

    public function postAvatar(int $id, AvatarRequest $request, ThumbnailService $thumbnailService, BaseHttpResponse $response)
    {
        try {
            $user = $this->userRepository->findOrFail($id);

            $result = RvMedia::handleUpload($request->file('avatar_file'), 0, 'users');

            if ($result['error']) {
                return $response->setError()->setMessage($result['message']);
            }

            $avatarData = json_decode($request->input('avatar_data'));

            $file = $result['data'];

            $thumbnailService
                ->setImage(RvMedia::getRealPath($file->url))
                ->setSize((int)$avatarData->width ?: 150, (int)$avatarData->height ?: 150)
                ->setCoordinates((int)$avatarData->x, (int)$avatarData->y)
                ->setDestinationPath(File::dirname($file->url))
                ->setFileName(File::name($file->url) . '.' . File::extension($file->url))
                ->save('crop');

            $this->fileRepository->forceDelete(['id' => $user->avatar_id]);

            $user->avatar_id = $file->id;

            $this->userRepository->createOrUpdate($user);

            return $response
                ->setMessage(trans('core/acl::users.update_avatar_success'))
                ->setData(['url' => RvMedia::url($file->url)]);
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getTheme(string $theme)
    {
        if (Auth::check() && ! app()->environment('demo')) {
            UserMeta::setMeta('admin-theme', $theme);
        }

        session()->put('admin-theme', $theme);

        try {
            return redirect()->back();
        } catch (Exception) {
            return redirect()->route('access.login');
        }
    }

    public function makeSuper(int $id, BaseHttpResponse $response)
    {
        try {
            $user = $this->userRepository->findOrFail($id);

            $user->updatePermission(ACL_ROLE_SUPER_USER, true);
            $user->updatePermission(ACL_ROLE_MANAGE_SUPERS, true);
            $user->super_user = 1;
            $user->manage_supers = 1;
            $this->userRepository->createOrUpdate($user);

            return $response
                ->setNextUrl(route('users.index'))
                ->setMessage(trans('core/base::system.supper_granted'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setNextUrl(route('users.index'))
                ->setMessage($exception->getMessage());
        }
    }

    public function removeSuper(int $id, Request $request, BaseHttpResponse $response)
    {
        if ($request->user()->getKey() == $id) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::system.cannot_revoke_yourself'));
        }

        $user = $this->userRepository->findOrFail($id);

        $user->updatePermission(ACL_ROLE_SUPER_USER, false);
        $user->updatePermission(ACL_ROLE_MANAGE_SUPERS, false);
        $user->super_user = 0;
        $user->manage_supers = 0;
        $this->userRepository->createOrUpdate($user);

        return $response
            ->setNextUrl(route('users.index'))
            ->setMessage(trans('core/base::system.supper_revoked'));
    }

    public function toggleSidebarMenu(Request $request, BaseHttpResponse $response)
    {
        $status = $request->input('status') == 'true';

        session()->put('sidebar-menu-toggle', $status ? Carbon::now() : '');

        return $response->setMessage($status);
    }
}
