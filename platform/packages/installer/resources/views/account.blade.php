@extends('packages/installer::master')

@section('template_title')
    {{ trans('packages/installer::installer.create_account') }}
@endsection

@section('title')
    <i class="fa fa-magic fa-fw" aria-hidden="true"></i>
    {{ trans('packages/installer::installer.create_account') }}
@endsection

@section('container')

    <form method="post" action="{{ route('installers.account.save') }}">
        @csrf

        <div class="form-group {{ $errors->has('first_name') ? ' has-error ' : '' }}">
            <label for="first_name">
                {{ trans('packages/installer::installer.first_name') }}
            </label>
            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                   placeholder="{{ trans('packages/installer::installer.first_name') }}"/>
            @if ($errors->has('first_name'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('first_name') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('last_name') ? ' has-error ' : '' }}">
            <label for="last_name">
                {{ trans('packages/installer::installer.last_name') }}
            </label>
            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                   placeholder="{{ trans('packages/installer::installer.last_name') }}"/>
            @if ($errors->has('last_name'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('last_name') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('username') ? ' has-error ' : '' }}">
            <label for="username">
                {{ trans('packages/installer::installer.username') }}
            </label>
            <input type="text" name="username" id="username" value="{{ old('username') }}"
                   placeholder="{{ trans('packages/installer::installer.username') }}"/>
            @if ($errors->has('username'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('username') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('email') ? ' has-error ' : '' }}">
            <label for="email">
                {{ trans('packages/installer::installer.email') }}
            </label>
            <input type="text" name="email" id="email" value="{{ old('email') }}"
                   placeholder="{{ trans('packages/installer::installer.email') }}"/>
            @if ($errors->has('email'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('email') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('password') ? ' has-error ' : '' }}">
            <label for="password">
                {{ trans('packages/installer::installer.password') }}
            </label>
            <input type="password" name="password" id="password" value="{{ old('password') }}"
                   placeholder="{{ trans('packages/installer::installer.password') }}"/>
            @if ($errors->has('password'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('password') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error ' : '' }}">
            <label for="password_confirmation">
                {{ trans('packages/installer::installer.password_confirmation') }}
            </label>
            <input type="password" name="password_confirmation" id="password_confirmation" value="{{ old('password_confirmation') }}"
                   placeholder="{{ trans('packages/installer::installer.password_confirmation') }}"/>
            @if ($errors->has('password_confirmation'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('password_confirmation') }}
                </span>
            @endif
        </div>

        <div class="buttons">
            <button class="button" type="submit">
                {{ trans('packages/installer::installer.create') }}
                <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
            </button>
        </div>
    </form>

@endsection
