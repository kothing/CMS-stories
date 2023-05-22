<?php

namespace Botble\Language\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Language;

class LocalizationRoutes extends LaravelLocalizationMiddlewareBase
{
    public function handle(Request $request, Closure $next)
    {
        // If the URL of the request is in exceptions.
        if ($this->shouldIgnore($request)) {
            return $next($request);
        }

        $routeName = Language::getRouteNameFromAPath($request->getUri());

        Language::setRouteName($routeName);

        return $next($request);
    }
}
