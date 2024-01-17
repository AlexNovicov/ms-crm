<?php

use App\Http\Controllers\Web\CrmController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/**
 * Админка.
 */
Route::get('/admin', function () {
    return view('admin');
})->name('admin');

/**
 * Обработка вебхука для получения токена.
 */
Route::group(['as' => 'crm.', 'prefix' => 'crm'], function () {
    Route::get('/get_token', [CrmController::class, 'get_token'])->name('get_token');
});

Route::get('/test', [\App\Http\Controllers\Web\TestController::class, 'test']);
