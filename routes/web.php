<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', ['middleware'=>'guest','uses'=>'\App\Http\Controllers\Auth\LoginRegisterController@showRegisterLoginForm']);
Route::get('/login', ['middleware'=>'guest','uses'=>'\App\Http\Controllers\Auth\LoginRegisterController@showRegisterLoginForm']);

//Auth::routes();

/**Authentication routes**/
Route::post('/login', ['middleware'=>'guest','uses'=>'\App\Http\Controllers\Auth\LoginRegisterController@loginOrRegister']);
Route::post('/logout',['as'=>'logout','uses'=> '\App\Http\Controllers\Auth\LoginRegisterController@logout']);
Route::post('/password/email',['middleware'=>'guest','uses'=> '\App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail']);
Route::get('/password/reset',['middleware'=>'guest','uses'=> '\App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm']);
Route::post('/password/reset',['middleware'=>'guest','uses'=> '\App\Http\Controllers\Auth\ResetPasswordController@reset']);
Route::get('/password/reset/{token}',['middleware'=>'guest','uses'=> '\App\Http\Controllers\Auth\ResetPasswordController@showResetForm']);

/**Home and Profile routes**/
Route::get('/home', 'HomeController@index')->middleware('auth');
Route::post('/profile/update/{id}', 'UserController@update')->middleware('auth');

/**Activation routes**/
Route::get('/activation/{token}', 'UserController@activateUser')->name('user.activate');
Route::get('/resend_activation', 'UserController@resendActivation')->middleware('auth')->middleware('throttle:3,5')->name('user.resend');

/**
Route::get('/teste',function(){

});
 **/
