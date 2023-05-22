<?php

namespace Botble\ACL\Http\Controllers\Auth;

use Assets;
use BaseHelper;
use Botble\ACL\Repositories\Interfaces\ActivationInterface;
use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\ACL\Traits\AuthenticatesUsers;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoginController extends BaseController
{
    use AuthenticatesUsers;

    protected string $redirectTo = '/';

    protected BaseHttpResponse $response;

    public function __construct(BaseHttpResponse $response)
    {
        $this->middleware('guest', ['except' => 'logout']);

        $this->redirectTo = BaseHelper::getAdminPrefix();
        $this->response = $response;
    }

    public function showLoginForm()
    {
        page_title()->setTitle(trans('core/acl::auth.login_title'));

        Assets::addScripts(['jquery-validation'])
            ->addScriptsDirectly('vendor/core/core/acl/js/login.js')
            ->addStylesDirectly('vendor/core/core/acl/css/animate.min.css')
            ->addStylesDirectly('vendor/core/core/acl/css/login.css')
            ->removeStyles([
                'select2',
                'fancybox',
                'spectrum',
                'simple-line-icons',
                'custom-scrollbar',
                'datepicker',
            ])
            ->removeScripts([
                'select2',
                'fancybox',
                'cookie',
            ]);

        return view('core/acl::auth.login');
    }

    public function login(Request $request)
    {
        $request->merge([$this->username() => $request->input('username')]);

        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $this->sendLockoutResponse($request);
        }

        $user = app(UserInterface::class)->getFirstBy([$this->username() => $request->input($this->username())]);
        if (! empty($user)) {
            if (! app(ActivationInterface::class)->completed($user)) {
                return $this->response
                    ->setError()
                    ->setMessage(trans('core/acl::auth.login.not_active'));
            }
        }

        if ($this->attemptLogin($request)) {
            app(UserInterface::class)->update(['id' => $user->id], ['last_login' => Carbon::now()]);
            if (! session()->has('url.intended')) {
                session()->flash('url.intended', url()->current());
            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to log in and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse();
    }

    public function username()
    {
        return filter_var(request()->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    }

    public function logout(Request $request)
    {
        do_action(AUTH_ACTION_AFTER_LOGOUT_SYSTEM, $request, $request->user());

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->response
            ->setNextUrl(route('access.login'))
            ->setMessage(trans('core/acl::auth.login.logout_success'));
    }
}
