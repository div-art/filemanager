<?php

use Illuminate\Http\Request;

Route::group(['prefix' => 'auth', 'namespace' => 'Divart\Filemanager\Http'] , function() {
    Route::post('login', 'FilemanagerController@login');
});

Route::group(['prefix' => 'filemanager', 'namespace' => 'Divart\Filemanager\Http', 'middleware' => 'jwt.auth'] , function() {

    Route::get('/', 'FilemanagerController@index');
    Route::post('folder/{folder?}', 'FilemanagerController@sort');

    Route::get('folder/{folder?}', 'FilemanagerController@getFolder')->where('folder','[\w-]+');
    Route::post('folder/create/{folder?}', 'FilemanagerController@createFolder')->where('folder','[\w-]+');
    Route::put('folder/update/{folder?}', 'FilemanagerController@updateFolder')->where('folder','[\w-]+');
    Route::delete('folder/delete/{folder?}', 'FilemanagerController@deleteFolder')->where('folder','[\w-]+');
    Route::post('folder/changelocation/{folder?}', 'FilemanagerController@changelocationFolder')->where('folder','[\w-]+');

    Route::get('file/{file}/folder/{folder?}', 'FilemanagerController@getFile')->where('folder', '[\w-]+');
    Route::post('file/create/{folder?}', 'FilemanagerController@createFile')->where('folder','[\w-]+');
    Route::put('file/update/{folder?}', 'FilemanagerController@updateFile')->where('folder','[\w-]+');
    Route::post('file/upload/{folder?}', 'FilemanagerController@uploadFile')->where('folder','[\w-]+');
    Route::delete('file/delete/{folder?}', 'FilemanagerController@deleteFile')->where('folder','[\w-]+');
    Route::post('file/changelocation/{folder?}', 'FilemanagerController@changelocationFile')->where('folder','[\w-]+');

});