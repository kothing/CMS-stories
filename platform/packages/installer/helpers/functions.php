<?php

if (! function_exists('get_active_menu_class_name')) {
    function get_active_menu_class_name(string|array $route, string $className = 'active'): ?string
    {
        if (is_array($route)) {
            return in_array(Route::currentRouteName(), $route) ? $className : '';
        }

        if (Route::currentRouteName() == $route) {
            return $className;
        }

        if (strpos(URL::current(), $route)) {
            return $className;
        }

        return null;
    }
}
