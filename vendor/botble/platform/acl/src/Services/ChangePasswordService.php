<?php

namespace Botble\ACL\Services;

use Botble\ACL\Models\User;
use Illuminate\Support\Facades\Auth;
use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\Support\Services\ProduceServiceInterface;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Throwable;

class ChangePasswordService implements ProduceServiceInterface
{
    protected UserInterface $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(Request $request): Exception|bool|User
    {
        if (! $request->user()->isSuperUser()) {
            if (! Hash::check($request->input('old_password'), $request->user()->getAuthPassword())) {
                return new Exception(trans('core/acl::users.current_password_not_valid'));
            }
        }

        $user = $this->userRepository->findOrFail($request->input('id', $request->user()->getKey()));

        $password = $request->input('password');

        $user->password = Hash::make($password);
        $this->userRepository->createOrUpdate($user);

        if ($user->id != $request->user()->getKey()) {
            try {
                Auth::setUser($user)->logoutOtherDevices($password);
            } catch (Throwable $exception) {
                info($exception->getMessage());
            }
        }

        do_action(USER_ACTION_AFTER_UPDATE_PASSWORD, USER_MODULE_SCREEN_NAME, $request, $user);

        return $user;
    }
}
