<?php
Route::get('ledger/sales_purchases', 'AccountsController@ledgerSalesAndPurchase');
Route::get('ledger/customer/{table}/{id}', 'AccountsController@customerLedger');
Route::get('ledger/purchase/{table}/{id}', 'AccountsController@customerPurchase');
Route::get('ledger/invoice/sale/{table}/{id}', 'AccountsController@saleInvoice');
Route::get('ledger/invoice/purchase/{table}/{id}', 'AccountsController@purchaseInvoice');



Route::middleware('AfterWork')->post('/insert', 'APIController@insert');

Route::post('logger/register', 'CallRecorderController@registerAgent');
Route::post('logger/login', 'CallRecorderController@login');
Route::post('logger/sim', 'CallRecorderController@sim_allocation');
// Route::post('logger/create', 'CallRecorderController@createLog')->middleware('IdentifyCallLog');
Route::post('logger/push_logs', 'CallRecorderController@createLogs')->middleware('IdentifyCallLog');
Route::get('logger/display', 'CallRecorderController@displayLog');
Route::get('logger/analytics', 'CallRecorderController@analytics');
Route::get('logger/agents', 'CallRecorderController@agents');
Route::get('logger/departments', 'CallRecorderController@departments');
Route::get('logger/alerts', 'CallRecorderController@departments');
Route::get('logger/websites', 'CallRecorderController@getWebsites');
Route::post('logger/pushToCRM', 'CallRecorderController@pushToCRM');
Route::post('logger/pushMultipleToCRM', 'CallRecorderController@pushMultipleToCRM');
Route::post('iscustomer', 'CallRecorderController@iscustomer');
