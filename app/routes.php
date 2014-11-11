<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
  return View::make('hello');
});

Route::get('/authtest', array('before' => 'auth.basic', function()
{
    return View::make('hello');
}));

// Route group for API versioning
Route::group(array('prefix' => 'api/v1', 'before' => 'auth.basic'), function()
{
    Route::resource('item', 'ItemController');
    Route::resource('set', 'SetController');

    Route::put('set/{setId}/item/{itemId}', 'SetController@addItem');
    Route::delete('set/{setId}/item/{itemId}', 'SetController@removeItem');
});
