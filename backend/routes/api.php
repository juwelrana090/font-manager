<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FontController;

Route::group(['prefix' => 'v1', 'middleware' => ['api']], function () {

    Route::group(['prefix' => 'font'], function () {
        Route::get('/', [FontController::class, 'index']);
        Route::post('/upload', [FontController::class, 'store']);
        Route::delete('/{id}', [FontController::class, 'destroy']);
    });

    Route::group(['prefix' => 'font-group'], function () {
        Route::get('/', [FontController::class, 'index']);
        Route::post('/upload', [FontController::class, 'store']);
        Route::delete('/{id}', [FontController::class, 'destroy']);
    });

});
