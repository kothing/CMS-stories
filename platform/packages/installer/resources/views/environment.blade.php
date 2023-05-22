@extends('packages/installer::master')

@section('template_title')
    {{ trans('packages/installer::installer.environment.wizard.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-magic fa-fw" aria-hidden="true"></i>
    {!! trans('packages/installer::installer.environment.wizard.title') !!}
@endsection

@section('container')

    <form method="post" action="{{ route('installers.environment.save') }}">
        @csrf

        <div class="form-group {{ $errors->has('app_name') ? ' has-error ' : '' }}">
            <label for="app_name">
                {{ trans('packages/installer::installer.environment.wizard.form.app_name_label') }}
            </label>
            <input type="text" name="app_name" id="app_name" value="{{ old('app_name', config('app.name')) }}"
                   placeholder="{{ trans('packages/installer::installer.environment.wizard.form.app_name_placeholder') }}"/>
            @if ($errors->has('app_name'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('app_name') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('app_url') ? ' has-error ' : '' }}">
            <label for="app_url">
                {{ trans('packages/installer::installer.environment.wizard.form.app_url_label') }}
            </label>
            <input type="url" name="app_url" id="app_url" value="{{ old('app_url', url('')) }}"
                   placeholder="{{ trans('packages/installer::installer.environment.wizard.form.app_url_placeholder') }}"/>
            @if ($errors->has('app_url'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('app_url') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('database_connection') ? ' has-error ' : '' }}">
            <label for="database_connection">
                {{ trans('packages/installer::installer.environment.wizard.form.db_connection_label') }}
            </label>
            <select name="database_connection" id="database_connection" class="form-select">
                <option value="mysql"
                        @if (old('database_connection', config('database.default')) === 'mysql') selected @endif>{{ trans('packages/installer::installer.environment.wizard.form.db_connection_label_mysql') }}</option>
                <option value="sqlite"
                        @if (old('database_connection', config('database.default')) === 'sqlite') selected @endif>{{ trans('packages/installer::installer.environment.wizard.form.db_connection_label_sqlite') }}</option>
                <option value="pgsql"
                        @if (old('database_connection', config('database.default')) === 'pgsql') selected @endif>{{ trans('packages/installer::installer.environment.wizard.form.db_connection_label_pgsql') }}</option>
            </select>
            @if ($errors->has('database_connection'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('database_connection') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('database_hostname') ? ' has-error ' : '' }}">
            <label for="database_hostname">
                {{ trans('packages/installer::installer.environment.wizard.form.db_host_label') }}
            </label>
            <input type="text" name="database_hostname" id="database_hostname" value="{{ old('database_hostname', config('database.connections.mysql.host')) }}"
                   placeholder="{{ trans('packages/installer::installer.environment.wizard.form.db_host_placeholder') }}"/>
            <span class="helper-block">
                {{ trans('packages/installer::installer.environment.wizard.form.db_host_helper') }}
            </span>
            @if ($errors->has('database_hostname'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('database_hostname') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('database_port') ? ' has-error ' : '' }}">
            <label for="database_port">
                {{ trans('packages/installer::installer.environment.wizard.form.db_port_label') }}
            </label>
            <input type="number" name="database_port" id="database_port" value="{{ old('database_port', config('database.connections.mysql.port')) }}"
                   placeholder="{{ trans('packages/installer::installer.environment.wizard.form.db_port_placeholder') }}"/>
            @if ($errors->has('database_port'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('database_port') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('database_name') ? ' has-error ' : '' }}">
            <label for="database_name">
                {{ trans('packages/installer::installer.environment.wizard.form.db_name_label') }}
            </label>
            <input type="text" name="database_name" id="database_name" value="{{ old('database_name') }}"
                   placeholder="{{ trans('packages/installer::installer.environment.wizard.form.db_name_placeholder') }}"/>
            @if ($errors->has('database_name'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('database_name') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('database_username') ? ' has-error ' : '' }}">
            <label for="database_username">
                {{ trans('packages/installer::installer.environment.wizard.form.db_username_label') }}
            </label>
            <input type="text" name="database_username" id="database_username" value="{{ old('database_username') }}"
                   placeholder="{{ trans('packages/installer::installer.environment.wizard.form.db_username_placeholder') }}"/>
            @if ($errors->has('database_username'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('database_username') }}
                </span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('database_password') ? ' has-error ' : '' }}">
            <label for="database_password">
                {{ trans('packages/installer::installer.environment.wizard.form.db_password_label') }}
            </label>
            <input type="password" name="database_password" id="database_password" value="{{ old('database_password') }}"
                   placeholder="{{ trans('packages/installer::installer.environment.wizard.form.db_password_placeholder') }}"/>
            @if ($errors->has('database_password'))
                <span class="error-block">
                    <i class="fa fa-fw fa-exclamation-triangle" aria-hidden="true"></i>
                    {{ $errors->first('database_password') }}
                </span>
            @endif
        </div>
        <div class="buttons">
            <button class="button" type="submit">
                {{ trans('packages/installer::installer.environment.wizard.form.buttons.install') }}
                <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
            </button>
        </div>
    </form>

@endsection
