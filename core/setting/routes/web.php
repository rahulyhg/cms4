<?php

Route::group(['namespace' => 'Botble\Setting\Http\Controllers', 'middleware' => 'web'], function () {
    Route::group(['prefix' => env('ADMIN_DIR'), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'settings/general'], function () {
            Route::get('/', [
                'as' => 'settings.options',
                'uses' => 'SettingController@getOptions',
                'permission' => 'settings.options',
            ]);

            Route::post('/edit', [
                'as' => 'settings.edit',
                'uses' => 'SettingController@postEdit',
                'permission' => 'settings.options',
            ]);
        });

        Route::group(['prefix' => 'settings/email', 'permission' => 'settings.options'], function () {
            Route::get('/', [
                'as' => 'settings.email',
                'uses' => 'SettingController@getEmailConfig',
            ]);

            Route::post('/edit', [
                'as' => 'settings.email.edit',
                'uses' => 'SettingController@postEditEmailConfig',
            ]);

            Route::get('/templates/edit/{type}/{name}/{template_file}', [
                'as' => 'setting.email.template.edit',
                'uses' => 'SettingController@getEditEmailTemplate',
            ]);

            Route::post('/template/edit', [
                'as' => 'setting.email.template.store',
                'uses' => 'SettingController@postStoreEmailTemplate',
            ]);

            Route::post('/template/reset-to-default', [
                'as' => 'setting.email.template.reset-to-default',
                'uses' => 'SettingController@postResetToDefault',
            ]);

            Route::post('/email/status', [
                'as' => 'setting.email.status.change',
                'uses' => 'SettingController@postChangeEmailStatus',
            ]);

            Route::post( '/email/test/send', [
                'as' => 'setting.email.send.test',
                'uses' => 'SettingController@postSendTestEmail',
            ]);
        });
    });
});
