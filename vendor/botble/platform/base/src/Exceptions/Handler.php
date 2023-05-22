<?php

namespace Botble\Base\Exceptions;

use App\Exceptions\Handler as ExceptionHandler;
use BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use EmailHandler;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Session\TokenMismatchException;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use RvMedia;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Theme;
use Throwable;

class Handler extends ExceptionHandler
{
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->ignore(PhpSpreadsheetException::class);
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof PostTooLargeException) {
            return RvMedia::responseError(trans('core/media::media.upload_failed', [
                'size' => BaseHelper::humanFilesize(RvMedia::getServerConfigMaxUploadFileSize()),
            ]));
        }

        if ($exception instanceof ModelNotFoundException || $exception instanceof MethodNotAllowedHttpException) {
            $exception = new NotFoundHttpException($exception->getMessage(), $exception);
        }

        if ($exception instanceof TokenMismatchException) {
            return (new BaseHttpResponse())
                ->setError()
                ->setCode($exception->getCode())
                ->setMessage('CSRF token mismatch. Please try again!');
        }

        if ($this->isHttpException($exception)) {
            $code = $exception->getStatusCode();

            if ($request->expectsJson()) {
                $response = new BaseHttpResponse();

                return match ($code) {
                    401 => $response
                        ->setError()
                        ->setMessage(trans('core/acl::permissions.access_denied_message'))
                        ->setCode($code)
                        ->toResponse($request),
                    403 => $response
                        ->setError()
                        ->setMessage(trans('core/acl::permissions.action_unauthorized'))
                        ->setCode($code)
                        ->toResponse($request),
                    404 => $response
                        ->setError()
                        ->setMessage(trans('core/base::errors.not_found'))
                        ->setCode(404)
                        ->toResponse($request),
                    default => $response
                        ->setError()
                        ->setMessage($exception->getMessage())
                        ->setCode($code)
                        ->toResponse($request),
                };
            }

            if (! app()->isDownForMaintenance()) {
                do_action(BASE_ACTION_SITE_ERROR, $code);
            }
        }

        if ($exception instanceof NotFoundHttpException && setting('redirect_404_to_homepage', 0) == 1) {
            return redirect(route('public.index'));
        }

        return parent::render($request, $exception);
    }

    public function report(Throwable $exception)
    {
        if ($this->shouldReport($exception) && ! $this->isExceptionFromBot()) {
            if (! app()->isLocal() && ! app()->runningInConsole() && ! app()->isDownForMaintenance()) {
                if (setting('enable_send_error_reporting_via_email', false) &&
                    setting('email_driver', config('mail.default')) &&
                    $exception instanceof Exception
                ) {
                    EmailHandler::sendErrorException($exception);
                }

                if (config('core.base.general.error_reporting.via_slack', false)) {
                    logger()->channel('slack')->critical(
                        $exception->getMessage() . ($exception->getPrevious() ? '(' . $exception->getPrevious() . ')' : null),
                        [
                            'Request URL' => request()->fullUrl(),
                            'Request IP' => request()->ip(),
                            'Request Method' => request()->method(),
                            'Exception Type' => get_class($exception),
                            'File Path' => ltrim(str_replace(base_path(), '', $exception->getFile()), '/') . ':' . $exception->getLine(),
                        ]
                    );
                }
            }
        }

        parent::report($exception);
    }

    /**
     * Determine if the exception is from the bot.
     */
    protected function isExceptionFromBot(): bool
    {
        $ignoredBots = config('core.base.general.error_reporting.ignored_bots', []);
        $agent = strtolower(request()->userAgent());

        if (empty($agent)) {
            return false;
        }

        foreach ($ignoredBots as $bot) {
            if (str_contains($agent, $bot)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the view used to render HTTP exceptions.
     * @param HttpExceptionInterface $exception
     * @return string
     */
    protected function getHttpExceptionView(HttpExceptionInterface $exception)
    {
        if (app()->runningInConsole() || request()->wantsJson() || request()->expectsJson()) {
            return parent::getHttpExceptionView($exception);
        }

        $code = $exception->getStatusCode();

        if (request()->is(BaseHelper::getAdminPrefix() . '/*') || request()->is(BaseHelper::getAdminPrefix())) {
            return 'core/base::errors.' . $code;
        }

        if (class_exists('Theme')) {
            $view = 'theme.' . Theme::getThemeName() . '::views.' . $code;

            if (view()->exists($view)) {
                return $view;
            }
        }

        return parent::getHttpExceptionView($exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return (new BaseHttpResponse())
                ->setError()
                ->setMessage($exception->getMessage())
                ->setCode(401)
                ->toResponse($request);
        }

        return redirect()->guest(route('access.login'));
    }
}
