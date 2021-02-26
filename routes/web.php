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

Route::get('/', function() {
    return view('static.index');
})->name('home');

Route::get('/blog', function() {
    return view('static.index');
})->name('blog');

Route::get('/pricing', function() {
    return view('static.index');
})->name('pricing');

Route::get('/privacy-policy', function() {
    return view('static.index');
})->name('privacy-policy');

Route::get('/terms-of-service', function() {
    return view('static.index');
})->name('terms-of-service');

Route::get('/legal-disclosures', function() {
    return view('static.index');
})->name('legal-disclosures');

Auth::routes();

Route::get('/dashboard', function() {
    return redirect('/dashboard/overview');
})->name('dashboard');

Route::get('/dashboard/{page}', [App\Http\Controllers\Dashboard\DashboardController::class, 'index']);



Route::middleware('auth')->prefix('auth')->group(function() {

    Route::prefix('analyse')->group(function() {
        Route::post('/url', [App\Http\Controllers\Dashboard\DashboardController::class, 'analyseUrl']);
    });

    Route::prefix('user')->group(function() {
        Route::post('/get-user', [App\Http\Controllers\Dashboard\UserController::class, 'getUser']);
        Route::post('/change-password', [App\Http\Controllers\Dashboard\UserController::class, 'changePassword']);
        Route::post('/change-name', [App\Http\Controllers\Dashboard\UserController::class, 'changeName']);
    });

    Route::prefix('team')->group(function() {
        Route::post('/get-all-teams', [App\Http\Controllers\Dashboard\TeamController::class, 'getAllTeams']);
        Route::post('/update-or-create-team', [App\Http\Controllers\Dashboard\TeamController::class, 'updateOrCreateTeam']);
    });

    Route::prefix('notifications')->group(function() {
        Route::post('/get-all-notifications', [App\Http\Controllers\Dashboard\NotificationController::class, 'getAllNotifications']);
    });
});
