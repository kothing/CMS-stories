<?php

use Botble\Gallery\Models\Gallery as GalleryModel;

Route::group(['namespace' => 'Botble\Gallery\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'galleries', 'as' => 'galleries.'], function () {
            Route::resource('', 'GalleryController')
                ->parameters(['' => 'gallery']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'GalleryController@deletes',
                'permission' => 'galleries.destroy',
            ]);
        });
    });

    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function () {
        $prefix = SlugHelper::getPrefix(GalleryModel::class, 'galleries');

        Route::get($prefix ?: 'galleries', [
            'as' => 'public.galleries',
            'uses' => 'PublicController@getGalleries',
        ]);

        if ($prefix) {
            Route::get($prefix . '/{slug}', [
                'as' => 'public.gallery',
                'uses' => 'PublicController@getGallery',
            ]);
        }
    });
});
