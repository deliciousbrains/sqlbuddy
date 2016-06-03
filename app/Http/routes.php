<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return view('table');
});

$app->group([
    'namespace' => 'App\Http\Controllers\Api',
    'prefix'    => 'api',
], function () use ($app) {
    // Database
    $app->get('/databases', 'DatabaseController@index');
    $app->post('/databases', 'DatabaseController@store');
    $app->delete('/databases/{id}', 'DatabaseController@destroy');

    // Table
    $app->get('/databases/{database}/tables', 'TableController@index');
    $app->get('/databases/{database}/tables/{table}', 'TableController@rows');
});
