<?php

return [
    'sessions' => 'Sessions',
    'visitors' => 'Visitors',
    'pageviews' => 'Pageviews',
    'bounce_rate' => 'Bounce Rate',
    'page_session' => 'Pages/Session',
    'avg_duration' => 'Avg. Duration',
    'percent_new_session' => 'Percent new session',
    'new_users' => 'New visitors',
    'visits' => 'visits',
    'views' => 'views',
    'property_id_not_specified' => 'You must provide a valid view id. The document here: <a href="https://docs.botble.com/cms/master/plugin-analytics" target="_blank">https://docs.botble.com/cms/master/plugin-analytics</a>',
    'credential_is_not_valid' => 'Analytics credentials is not valid. The document here: <a href="https://docs.botble.com/cms/master/plugin-analytics" target="_blank">https://docs.botble.com/cms/master/plugin-analytics</a>',
    'start_date_can_not_before_end_date' => 'Start date :start_date cannot be after end date :end_date',
    'wrong_configuration' => 'To view analytics you\'ll need to get a Google Analytics client id and add it to your settings. <br /> You also need JSON credential data. <br /> The document here: <a href="https://docs.botble.com/cms/master/plugin-analytics" target="_blank">https://docs.botble.com/cms/master/plugin-analytics</a>',

    'settings' => [
        'title' => 'Google Analytics',
        'description' => 'Config Credentials for Google Analytics',
        'tracking_code' => 'Tracking ID',
        'tracking_code_placeholder' => 'Example: GA-12586526-8',
        'view_id' => 'View ID for UA',
        'view_id_description' => 'Google Analytics View ID (UA)',
        'analytics_property_id' => 'Property ID for GA4',
        'analytics_property_id_description' => 'Google Analytics Property ID (GA4)',
        'json_credential' => 'Service Account Credentials',
        'json_credential_description' => 'Service Account Credentials',
        'type' => 'Type',
        'ua_description' => 'Universal Analytics',
        'ga4_description' => 'Google Analytics 4 (GA4)',
    ],

    'widget_analytics_page' => 'Top Most Visit Pages',
    'widget_analytics_browser' => 'Top Browsers',
    'widget_analytics_referrer' => 'Top Referrers',
    'widget_analytics_general' => 'Site Analytics',
];
