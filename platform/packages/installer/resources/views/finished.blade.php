@extends('packages/installer::master')

@section('template_title')
    {{ trans('packages/installer::installer.final.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-flag-checkered fa-fw" aria-hidden="true"></i>
    {{ trans('packages/installer::installer.final.title') }}
@endsection

@section('container')

    <p class="text-center">{{ trans('packages/installer::installer.install_success') }}</p>

    <div class="buttons">
        <a href="{{ route('access.login') }}" class="button">{{ trans('packages/installer::installer.final.exit') }}</a>
    </div>

@endsection
