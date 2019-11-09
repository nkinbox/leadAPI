<?php

use Illuminate\Http\Request;

Route::middleware('AfterWork')->post('/insert', 'APIController@insert');

Route::post('logger/register', 'CallRecorderController@registerAgent');
Route::post('logger/login', 'CallRecorderController@login');
Route::post('logger/sim', 'CallRecorderController@sim_allocation');
Route::post('logger/create', 'CallRecorderController@createLog');
Route::post('logger/push_logs', 'CallRecorderController@createLogs');
Route::get('logger/display', 'CallRecorderController@displayLog');
Route::get('logger/analytics', 'CallRecorderController@analytics');
Route::get('logger/agents', 'CallRecorderController@agents');
Route::get('logger/departments', 'CallRecorderController@departments');
