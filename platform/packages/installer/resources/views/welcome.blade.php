@extends('packages/installer::master')

@section('template_title')
    {{ trans('packages/installer::installer.welcome.templateTitle') }}
@endsection

@section('title')
    {{ trans('packages/installer::installer.welcome.title') }}
@endsection

@section('container')
    <p class="text-center">
        {{ trans('packages/installer::installer.welcome.message') }}
    </p>
    <p class="text-center">
        <a href="{{ URL::signedRoute('installers.requirements', [], \Carbon\Carbon::now()->addMinutes(30)) }}" class="button">
            {{ trans('packages/installer::installer.welcome.next') }}
            <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
        </a>
    </p>
@endsection
