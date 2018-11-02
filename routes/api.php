<?php

use Illuminate\Http\Request;

Route::middleware('AfterWork')->post('/insert', 'APIController@insert');
