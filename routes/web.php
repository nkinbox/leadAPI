<?php
Route::get('/', function () {
    return view('welcome');
});
Route::get('/callLogs', function () {
    return view('call_log');
});
Auth::routes();

Route::middleware('auth')->group(function() {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('columns/{table_id}/{operation?}/{id?}', 'ColumnController@index')->name('column');
    Route::post('columns', 'ColumnController@create')->name('column.create');
    Route::put('columns', 'ColumnController@update')->name('column.update');
    Route::delete('columns', 'ColumnController@delete')->name('column.delete');

    Route::get('tables/{operation?}/{id?}', 'TableController@index')->name('table');
    Route::post('tables', 'TableController@create')->name('table.create');
    Route::put('tables', 'TableController@update')->name('table.update');
    Route::delete('tables', 'TableController@delete')->name('table.delete');

    Route::get('form_request/{operation?}/{id?}', 'FormRequestController@index')->name('formRequest');
    Route::post('form_request', 'FormRequestController@create')->name('formRequest.create');
    Route::put('form_request', 'FormRequestController@update')->name('formRequest.update');
    Route::delete('form_request', 'FormRequestController@delete')->name('formRequest.delete');

    Route::get('form_field/{form_id}/{operation?}/{id?}', 'FormFieldController@index')->name('formField');
    Route::post('form_field', 'FormFieldController@create')->name('formField.create');
    Route::put('form_field', 'FormFieldController@update')->name('formField.update');
    Route::delete('form_field', 'FormFieldController@delete')->name('formField.delete');

    Route::get('request_map/{form_id}/{operation?}/{id?}', 'FormMapController@index')->name('requestMap');
    Route::post('request_map', 'FormMapController@create')->name('requestMap.create');
    Route::put('request_map', 'FormMapController@update')->name('requestMap.update');
    Route::delete('request_map', 'FormMapController@delete')->name('requestMap.delete');

    Route::get('request_route/{map_id}', 'FormRequestRouteController@index')->name('requestRoute');
    Route::post('request_route', 'FormRequestRouteController@create')->name('requestRoute.create');
    
    Route::get('websites', 'WebsiteMapController@all')->name('website');

    Route::get('website_map/{website_id}/{operation?}/{id?}', 'WebsiteMapController@index')->name('websiteMap');
    Route::post('website_map', 'WebsiteMapController@create')->name('websiteMap.create');
    Route::delete('website_map', 'WebsiteMapController@delete')->name('websiteMap.delete');
});
