<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
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

Broadcast::routes();

Route::get('/', function() {
    return view('static.index');
})->name('home');

Route::get('/resources', function() {
    return view('static.resources');
})->name('resources');

Route::get('/pricing', function() {
    return view('static.pricing');
})->name('pricing');

Route::get('/privacy-policy', function() {
    return view('static.privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-service', function() {
    return view('static.terms-of-service');
})->name('terms-of-service');

Route::get('/legal-disclosures', function() {
    return view('static.legal-disclosures');
})->name('legal-disclosures');

Auth::routes();

Route::get('/dashboard', function() {
    return redirect('/dashboard/overview');
})->name('dashboard');

Route::get('/dashboard/{page}', [App\Http\Controllers\Dashboard\DashboardController::class, 'index']);



Route::middleware('auth')->prefix('auth')->group(function() {

    Route::prefix('reports')->group(function() {
        Route::post('/get-paginated-report-groups', [App\Http\Controllers\Dashboard\ReportController::class, 'getPaginatedReportGroups']);
        Route::post('/request-site-analysis', [App\Http\Controllers\Dashboard\ReportController::class, 'requestSiteAnalysis']);
        Route::post('/delete-report', [App\Http\Controllers\Dashboard\ReportController::class, 'deleteReport']);
        Route::post('/share-report', [App\Http\Controllers\Dashboard\ReportController::class, 'shareReport']);
    });

    Route::prefix('user')->group(function() {
        Route::post('/get-user', [App\Http\Controllers\Dashboard\UserController::class, 'getUser']);
        Route::post('/change-password', [App\Http\Controllers\Dashboard\UserController::class, 'changePassword']);
        Route::post('/change-name', [App\Http\Controllers\Dashboard\UserController::class, 'changeName']);
        Route::post('/close-account', [App\Http\Controllers\Dashboard\UserController::class, 'closeAccount']);
    });

    Route::prefix('team')->group(function() {
        Route::post('/get-all-teams', [App\Http\Controllers\Dashboard\TeamController::class, 'getAllTeams']);
        Route::post('/create-team', [App\Http\Controllers\Dashboard\TeamController::class, 'createTeam']);
        Route::post('/get-all-invites', [App\Http\Controllers\Dashboard\TeamController::class, 'getAllInvites']);
        Route::post('/handle-invite', [App\Http\Controllers\Dashboard\TeamController::class, 'handleInvite']);
        
        Route::group(['middleware' => 'team.auth:member'], function() {
            Route::post('/leave-team', [App\Http\Controllers\Dashboard\TeamController::class, 'leaveTeam']);
            Route::post('/set-active-team-id', [App\Http\Controllers\Dashboard\TeamController::class, 'setActiveTeamId']);
        });
        
        Route::group(['middleware' => 'team.auth:owner'], function() {
            Route::post('/update-team', [App\Http\Controllers\Dashboard\TeamController::class, 'updateTeam']);
            Route::post('/delete-team', [App\Http\Controllers\Dashboard\TeamController::class, 'deleteTeam']);
            Route::post('/create-invite', [App\Http\Controllers\Dashboard\TeamController::class, 'createInvite']);
            Route::post('/create-team-site', [App\Http\Controllers\Dashboard\TeamController::class, 'createTeamSite']);
            Route::post('/delete-team-site', [App\Http\Controllers\Dashboard\TeamController::class, 'deleteTeamSite']);
            Route::post('/delete-member', [App\Http\Controllers\Dashboard\TeamController::class, 'deleteMember']);
        });
    });

    Route::prefix('notifications')->group(function() {
        Route::post('/get-all-notifications', [App\Http\Controllers\Dashboard\NotificationController::class, 'getAllNotifications']);
    });

    Route::prefix('subscriptions')->group(function() {
        Route::get('/billing-portal', [App\Http\Controllers\Dashboard\SubscriptionController::class, 'redirectToBillingPortal']);
        Route::post('/get-setup-intent', [App\Http\Controllers\Dashboard\SubscriptionController::class, 'getSetupIntent']);
        Route::post('/create-test-subscription', [App\Http\Controllers\Dashboard\SubscriptionController::class, 'testSub']);
    });
});
