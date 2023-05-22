<div class="flexbox-annotated-section">
    <div class="flexbox-annotated-section-annotation">
        <div class="annotated-section-title pd-all-20">
            <h2>{{ trans('plugins/analytics::analytics.settings.title') }}</h2>
        </div>
        <div class="annotated-section-description pd-all-20 p-none-t">
            <p class="color-note">{{ trans('plugins/analytics::analytics.settings.description') }}</p>
        </div>
    </div>

    <div class="flexbox-annotated-section-content">
        <div class="wrapper-content pd-all-20">
            <div class="form-group mb-3">
                <label class="text-title-field"
                       for="google_analytics">{{ trans('plugins/analytics::analytics.settings.tracking_code') }}</label>
                <input data-counter="120" type="text" class="next-input" name="google_analytics" id="google_analytics"
                       value="{{ setting('google_analytics') }}" placeholder="{{ trans('plugins/analytics::analytics.settings.tracking_code_placeholder') }}">
            </div>
            <div class="form-group mb-3">
                <label class="text-title-field"
                       for="analytics_type">{{ trans('plugins/analytics::analytics.settings.type') }}
                </label>
                <label class="me-2">
                    <input type="radio" name="analytics_type"
                           value="ua"
                           @if (setting('analytics_type', 'ua') == 'ua') checked @endif>{{ trans('plugins/analytics::analytics.settings.ua_description') }}
                </label>
                <label>
                    <input type="radio" name="analytics_type"
                           value="ga4"
                           @if (setting('analytics_type', 'ua') == 'ga4') checked @endif>{{ trans('plugins/analytics::analytics.settings.ga4_description') }}
                </label>
            </div>

            <div class="form-group mb-3">
                <label class="text-title-field"
                       for="analytics_view_id">{{ trans('plugins/analytics::analytics.settings.view_id') }}</label>
                <input data-counter="120" type="text" class="next-input" name="analytics_view_id" id="analytics_view_id"
                       value="{{ setting('analytics_view_id') }}" placeholder="{{ trans('plugins/analytics::analytics.settings.view_id_description') }}">
            </div>
            <div class="form-group mb-3">
                <label class="text-title-field"
                       for="analytics_property_id">{{ trans('plugins/analytics::analytics.settings.analytics_property_id') }}</label>
                <input data-counter="120" type="text" class="next-input" name="analytics_property_id" id="analytics_property_id"
                       value="{{ setting('analytics_property_id') }}" placeholder="{{ trans('plugins/analytics::analytics.settings.analytics_property_id_description') }}">
            </div>

            @if (!app()->environment('demo'))
                <div class="form-group mb-3">
                    <label class="text-title-field"
                           for="analytics_service_account_credentials">{{ trans('plugins/analytics::analytics.settings.json_credential') }}</label>
                    <textarea class="next-input form-control" name="analytics_service_account_credentials" id="analytics_service_account_credentials" rows="5" placeholder="{{ trans('plugins/analytics::analytics.settings.json_credential_description') }}">{{ setting('analytics_service_account_credentials') }}</textarea>
                </div>
            @endif
        </div>
    </div>
</div>
