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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::prefix('webhooks')->group(function() {
    Route::prefix('reports')->group(function() {
        Route::post('/status-crawling', [App\Http\Controllers\Dashboard\ReportController::class, 'statusUpdate']);
        Route::post('/status-crawling-completed', [App\Http\Controllers\Dashboard\ReportController::class, 'statusUpdate']);
        Route::post('/status-fetching', [App\Http\Controllers\Dashboard\ReportController::class, 'statusUpdate']);
        Route::post('/status-fetching-completed', [App\Http\Controllers\Dashboard\ReportController::class, 'statusUpdate']);
        Route::post('/status-completed', [App\Http\Controllers\Dashboard\ReportController::class, 'statusUpdate']);
        Route::post('/add-page', [App\Http\Controllers\Dashboard\ReportController::class, 'addPage']);
    });
});