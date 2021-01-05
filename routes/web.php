<?php

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

Route::get('/', 'Controller@webIndex')->name('web.index');
Route::get('/info', 'Controller@webPhpInfo')->name('web.info');
Route::get('/opcache', 'Controller@webOpcache')->name('web.opcache');
Route::get('/config', 'Controller@webConfig')->name('web.config');
Route::get('/worker', 'Controller@webWorker')->name('web.worker');
Route::get('/models', 'Controller@webModels')->name('web.models');
