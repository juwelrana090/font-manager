<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FontController;
use App\Http\Controllers\FontGroupController;

Route::group(['prefix' => 'v1', 'middleware' => ['api']], function () {

    Route::group(['prefix' => 'font'], function () {
        Route::get('/', [FontController::class, 'index']);
        Route::post('/upload', [FontController::class, 'store']);
        Route::delete('/{id}', [FontController::class, 'destroy']);
    });

    Route::prefix('font-group')->group(function () {
        Route::get('/', [FontGroupController::class, 'index']);
        Route::post('/', [FontGroupController::class, 'store']);
        Route::get('/{fontGroup}', [FontGroupController::class, 'show']);
        Route::put('/{id}', [FontGroupController::class, 'update']);
        Route::delete('/{id}', [FontGroupController::class, 'destroy']);
    });
});
