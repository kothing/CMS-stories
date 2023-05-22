<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('template_title', trans('packages/installer::installer.title'))</title>

    <link rel="icon" href="{{ asset('vendor/core/core/base/images/favicon.png') }}">
    <link href="{{ asset('vendor/core/core/base/libraries/font-awesome/css/fontawesome.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('vendor/core/packages/installer/css/style.css') }}?v={{ get_cms_version() }}" rel="stylesheet"/>

    <link rel="preconnect" href="{{ BaseHelper::getGoogleFontsURL() }}">
    <link href="{{ BaseHelper::getGoogleFontsURL() }}/css?family=Lato:400,700%7cPoppins:200,400,500,700" rel="stylesheet">

    @yield('style')
</head>
<body>
    <div class="master">
        <div class="box">
            <div class="header">
                <h1 class="header__title">@yield('title')</h1>
            </div>
            <ul class="step">
                <li class="step__divider"></li>
                <li class="step__item {{ get_active_menu_class_name('installers.final') }}">
                    <i class="step__icon fa fa-server" aria-hidden="true"></i>
                </li>
                <li class="step__divider"></li>
                <li class="step__item {{ get_active_menu_class_name('installers.environment')}}">
                    @if (Request::is('install/environment') || Request::is('install/environment') || Request::is('install/environment/save') )
                        <a href="{{ URL::signedRoute('installers.environment', [], \Carbon\Carbon::now()->addMinutes(30)) }}">
                            <i class="step__icon fa fa-cog" aria-hidden="true"></i>
                        </a>
                    @else
                        <i class="step__icon fa fa-cog" aria-hidden="true"></i>
                    @endif
                </li>
                <li class="step__divider"></li>
                <li class="step__divider"></li>
                <li class="step__item {{ get_active_menu_class_name('installers.requirements') }}">
                    @if (Request::is('install') || Request::is('install/requirements') || Request::is('install/environment') || Request::is('install/environment/save') )
                        <a href="{{ URL::signedRoute('installers.requirements', [], \Carbon\Carbon::now()->addMinutes(30)) }}">
                            <i class="step__icon fa fa-list" aria-hidden="true"></i>
                        </a>
                    @else
                        <i class="step__icon fa fa-list" aria-hidden="true"></i>
                    @endif
                </li>
                <li class="step__divider"></li>
                <li class="step__item {{ get_active_menu_class_name('installers.welcome') }}">
                    @if (Request::is('install') || Request::is('install/requirements') || Request::is('install/environment') || Request::is('install/environment/save') )
                        <a href="{{ URL::signedRoute('installers.welcome', [], \Carbon\Carbon::now()->addMinutes(30)) }}">
                            <i class="step__icon fa fa-home" aria-hidden="true"></i>
                        </a>
                    @else
                        <i class="step__icon fa fa-home" aria-hidden="true"></i>
                    @endif
                </li>
                <li class="step__divider"></li>
            </ul>
            <div class="main">
                @if (session('message'))
                    <p class="alert text-center">
                        <strong>
                            @if (is_array(session('message')))
                                {{ session('message')['message'] }}
                            @else
                                {{ session('message') }}
                            @endif
                        </strong>
                    </p>
                @endif
                @if (session()->has('errors'))
                    <div class="alert alert-danger" id="error_alert">
                        <button type="button" class="close" id="close_alert" data-dismiss="alert" aria-hidden="true">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                        <h4>
                            <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                            {{ trans('packages/installer::installer.forms.errorTitle') }}
                        </h4>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('container')
            </div>
        </div>
    </div>
    <script src="{{ asset('vendor/core/packages/installer/js/script.js') }}?v={{ get_cms_version() }}"></script>

    @yield('scripts')
</body>
</html>
