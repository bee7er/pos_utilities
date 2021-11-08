<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

# Front end
Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index');

Route::get('/printing/', function() {
    return view('printing');
});
Route::get('/finding/', function() {
    return view('finding');
});

Route::post('/api/search/{productCode}', 'API\ProductController@search');
Route::post('/api/download', 'API\ProductController@download');
Route::post('/api/refresh', 'API\ProductController@refresh');
Route::post('/api/verify', 'API\ProductController@verify');

# Add all the login and registration routes, although here I am avoiding the
# registration of new users and the reset of a new password
Auth::routes([
    'register' => true, // Registration Routes...
    'reset' => true, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
]);

# Admin dashboard
Route::get('/admin', 'Admin\AdminController@index')->name('admin');
Route::post('/admin', 'Admin\AdminController@action')->name('admin');
