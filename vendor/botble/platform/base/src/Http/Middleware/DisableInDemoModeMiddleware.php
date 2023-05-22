<?php

namespace Botble\Base\Http\Middleware;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class DisableInDemoModeMiddleware
{
    protected Application $app;

    protected BaseHttpResponse $httpResponse;

    public function __construct(Application $application, BaseHttpResponse $response)
    {
        $this->app = $application;
        $this->httpResponse = $response;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($this->app->environment('demo')) {
            return $this->httpResponse
                ->setError()
                ->withInput()
                ->setMessage(trans('core/base::system.disabled_in_demo_mode'))
                ->toResponse($request);
        }

        return $next($request);
    }
}
