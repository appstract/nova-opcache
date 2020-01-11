<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('/clear', 'NovaOpcacheController@clear');

Route::get('/config', 'NovaOpcacheController@config');

Route::get('/status', 'NovaOpcacheController@status');

Route::get('/compile', 'NovaOpcacheController@compile');
