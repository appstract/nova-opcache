<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Appstract\Opcache\OpcacheFacade as OPcache;

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

Route::get('/status', function (Request $request) {
    return response()->json(OPcache::getStatus());
});

Route::get('/config', function (Request $request) {
    return response()->json(OPcache::getConfig());
});

Route::get('/clear', function (Request $request) {
    return response()->json(OPcache::clear());
});

Route::get('/compile', function (Request $request) {
    return response()->json(OPcache::compile(true));
});
