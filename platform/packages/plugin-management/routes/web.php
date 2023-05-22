<?php

Route::group(['namespace' => 'Botble\PluginManagement\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'plugins'], function () {
            Route::get('', [
                'as' => 'plugins.index',
                'uses' => 'PluginManagementController@index',
            ]);

            Route::put('status', [
                'as' => 'plugins.change.status',
                'uses' => 'PluginManagementController@update',
                'middleware' => 'preventDemo',
                'permission' => 'plugins.index',
            ]);

            Route::delete('{plugin}', [
                'as' => 'plugins.remove',
                'uses' => 'PluginManagementController@destroy',
                'middleware' => 'preventDemo',
                'permission' => 'plugins.index',
            ]);
        });

        Route::group(['prefix' => 'plugins/marketplace'], function () {
            Route::get('', [
                'as' => 'plugins.marketplace',
                'uses' => 'MarketplaceController@index',
            ]);

            Route::group(['prefix' => 'ajax'], function () {
                Route::get('plugins', [
                    'as' => 'plugins.marketplace.ajax.list',
                    'uses' => 'MarketplaceController@list',
                ]);

                Route::get('{id}', [
                    'as' => 'plugins.marketplace.ajax.detail',
                    'uses' => 'MarketplaceController@detail',
                ]);

                Route::get('{id}/iframe', [
                    'as' => 'plugins.marketplace.ajax.iframe',
                    'uses' => 'MarketplaceController@iframe',
                ]);

                Route::post('{id}/install', [
                    'as' => 'plugins.marketplace.ajax.install',
                    'uses' => 'MarketplaceController@install',
                    'middleware' => 'preventDemo',
                ]);

                Route::post('{id}/update', [
                    'as' => 'plugins.marketplace.ajax.update',
                    'uses' => 'MarketplaceController@update',
                    'middleware' => 'preventDemo',
                ]);

                Route::post('/check-update', [
                    'as' => 'plugins.marketplace.ajax.check-update',
                    'uses' => 'MarketplaceController@checkUpdate',
                ]);
            });
        });
    });
});
