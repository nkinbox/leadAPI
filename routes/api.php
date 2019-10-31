<?php

use Illuminate\Http\Request;

Route::middleware('AfterWork')->post('/insert', 'APIController@insert');

Route::post('logger/register', 'CallRecorderController@registerAgent');
Route::post('logger/login', 'CallRecorderController@login');
Route::post('logger/sim', 'CallRecorderController@sim_allocation');
Route::post('logger/create', 'CallRecorderController@createLog');
Route::get('logger/display', 'CallRecorderController@displayLog');
