@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div id="plugin-list">
        @if (config('core.base.general.enable_marketplace_feature'))
            <div class="mb-3">
                <a class="btn-add-plugin" href="{{ route('plugins.marketplace') }}">
                    <i class="fa fa-plus me-3"></i> {{ trans('packages/plugin-management::plugin.plugins_add_new') }}
                </a>
            </div>
        @endif

        <div class="clearfix app-grid--blank-slate row">
            @foreach ($list as $plugin)
                <div class="app-card-item col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="app-item app-{{ $plugin->path }}">
                        <div class="app-icon">
                            @if ($plugin->image)
                                <img src="data:image/png;base64,{{ $plugin->image }}" alt="{{ $plugin->name }}">
                            @endif
                        </div>
                        <div class="app-details">
                            <h4 class="app-name">{{ $plugin->name }}</h4>
                        </div>
                        <div class="app-footer">
                            <div class="app-description" title="{{ $plugin->description }}">{{ $plugin->description }}</div>
                            @if (!config('packages.plugin-management.general.hide_plugin_author', false))
                                <div class="app-author">{{ trans('packages/plugin-management::plugin.author') }}: <a href="{{ $plugin->url }}" target="_blank">{{ $plugin->author }}</a></div>
                            @endif
                            <div class="app-version">{{ trans('packages/plugin-management::plugin.version') }}: {{ $plugin->version }}</div>
                            <div class="app-actions">
                                @if (Auth::user()->hasPermission('plugins.edit'))
                                    <button class="btn @if ($plugin->status) btn-warning @else btn-info @endif btn-trigger-change-status" data-plugin="{{ $plugin->path }}" data-status="{{ $plugin->status }}">
                                        @if ($plugin->status)
                                            {{ trans('packages/plugin-management::plugin.deactivate') }}
                                        @else
                                            {{ trans('packages/plugin-management::plugin.activate') }}
                                        @endif
                                    </button>
                                @endif

                                @if (Auth::user()->hasPermission('plugins.remove'))
                                    <button class="btn btn-danger btn-trigger-remove-plugin" data-plugin="{{ $plugin->path }}">{{ trans('packages/plugin-management::plugin.remove') }}</button>
                                @endif

                                <button class="btn btn-success btn-trigger-update-plugin" style="display: none;" data-check-update="{{ $plugin->id ?? 'plugin-' . $plugin->path }}" data-version="{{ $plugin->version }}">{{ trans('packages/plugin-management::plugin.update') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {!! Form::modalAction('remove-plugin-modal', trans('packages/plugin-management::plugin.remove_plugin'), 'danger', trans('packages/plugin-management::plugin.remove_plugin_confirm_message'), 'confirm-remove-plugin-button', trans('packages/plugin-management::plugin.remove_plugin_confirm_yes')) !!}
@stop
