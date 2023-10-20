<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['middleware' => 'api', 'limit' => 300, 'expires' => 10, 'namespace' => 'App\Http\Controllers'],
    function ($api) {
        $api->post('/{user}/send', ['as' => 'email.send', 'uses' => '\App\Http\Controllers\EmailController@send']);
        $api->get('/list', ['as' => 'email.list', 'uses' => '\App\Http\Controllers\EmailController@list']);
    });
