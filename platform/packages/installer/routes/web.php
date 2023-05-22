<?php

use Botble\Installer\Http\Controllers\InstallController;

Route::group([
    'prefix' => 'install',
    'as' => 'installers.',
    'middleware' => ['web', 'core'],
], function () {
    Route::group(['middleware' => 'install'], function () {
        Route::get('/', [InstallController::class, 'getWelcome'])->name('welcome');

        Route::get('requirements', [InstallController::class, 'getRequirements'])->name('requirements');

        Route::get('environment', [InstallController::class, 'getEnvironment'])->name('environment');

        Route::post('environment/save', [InstallController::class, 'postSaveEnvironment'])->name('environment.save');
    });

    Route::group(['middleware' => 'installing'], function () {
        Route::get('account', [InstallController::class, 'getCreateAccount'])->name('create_account');

        Route::post('account/save', [InstallController::class, 'postSaveAccount'])->name('account.save');

        Route::get('final', [InstallController::class, 'getFinish'])->name('final');
    });
});
