<?php

use App\Mail\TestEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

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

// Route::get('/mail', function() {
//     Mail::to('maurice.freuwoert@gmail.com')->send(new TestEmail());
// });

Broadcast::routes();
Auth::routes(['verify' => true]);

Route::view('/', 'static.index')->name('home');
Route::view('/pricing', 'static.pricing')->name('pricing');
Route::view('/branding', 'static.branding')->name('branding');

Route::prefix('tools')->group(function() {
    Route::view('/', 'static.tools.overview')->name('tools');
    Route::view('/metadata', 'static.tools.metadata-tool')->name('metadata-tools');
});

Route::prefix('resources')->group(function() {
    Route::get('/', [App\Http\Controllers\BlogController::class, 'allArticles'])->name('resources');
    Route::get('/category', [App\Http\Controllers\BlogController::class, 'allCategories'])->name('categories');
    Route::get('/category/{category}', [App\Http\Controllers\BlogController::class, 'category'])->name('category');
    Route::get('/author', [App\Http\Controllers\BlogController::class, 'allAuthors'])->name('authors');
    Route::get('/author/{author}', [App\Http\Controllers\BlogController::class, 'author'])->name('author');
    Route::get('/{article}', [App\Http\Controllers\BlogController::class, 'article'])->name('article');
});

Route::get('/go-pro/{plan}', [App\Http\Controllers\SubscriptionController::class, 'goPro'])->name('go-pro');

// Legal Stuff
Route::view('/privacy-policy', 'static.privacy-policy')->name('privacy-policy');
Route::view('/terms-of-service', 'static.terms-of-service')->name('terms-of-service');
Route::view('/legal-disclosures', 'static.legal-disclosures')->name('legal-disclosures');

// Guest Analysis
Route::post('/request-guest-site-analysis', [App\Http\Controllers\Dashboard\ReportController::class, 'requestGuestSiteAnalysis']);
Route::get('/report/{reportGroupId}', [App\Http\Controllers\Dashboard\ReportController::class, 'showGuestReport']);

// Get meta tags as guest
Route::post('/get-meta-data', [App\Http\Controllers\Dashboard\ReportController::class, 'getMetaData']);





Route::middleware('guest')->group(function() {
    Route::view('/mfa', 'auth.mfa')->name('mfa');
    Route::post('/verify-mfa', [App\Http\Controllers\Dashboard\UserController::class, 'TOTPMFA']);
});

Route::middleware(['auth', 'mfa'])->group(function() {
    Route::get('/dashboard', function() {
        return redirect('/dashboard/overview');
    })->name('dashboard');
    
    Route::get('/dashboard/{page}', [App\Http\Controllers\Dashboard\DashboardController::class, 'index']);
});



Route::middleware(['auth', 'mfa:api'])->prefix('auth')->group(function() {

    Route::post('/go-pro/complete', [App\Http\Controllers\SubscriptionController::class, 'complete']);

    Route::prefix('reports')->group(function() {
        Route::post('/get-paginated-report-groups', [App\Http\Controllers\Dashboard\ReportController::class, 'getPaginatedReportGroups']);
        Route::post('/delete-report', [App\Http\Controllers\Dashboard\ReportController::class, 'deleteReport']);
        Route::post('/share-report', [App\Http\Controllers\Dashboard\ReportController::class, 'shareReport']);
        
        Route::group(['middleware' => 'team.auth:member'], function() {
            Route::post('/request-site-analysis', [App\Http\Controllers\Dashboard\ReportController::class, 'requestSiteAnalysis']);
        });
    });

    Route::prefix('user')->group(function() {
        Route::post('/get-user', [App\Http\Controllers\Dashboard\UserController::class, 'getUser']);
        Route::post('/change-password', [App\Http\Controllers\Dashboard\UserController::class, 'changePassword']);
        Route::post('/change-name', [App\Http\Controllers\Dashboard\UserController::class, 'changeName']);
        Route::post('/close-account', [App\Http\Controllers\Dashboard\UserController::class, 'closeAccount']);
        // Multi Factor Authentication
        Route::post('/set-mfa-status', [App\Http\Controllers\Dashboard\UserController::class, 'setMFAStatus']);
        Route::post('/setup-totp-mfa', [App\Http\Controllers\Dashboard\UserController::class, 'setupTOTPMFA']);
        Route::post('/delete-totp-mfa', [App\Http\Controllers\Dashboard\UserController::class, 'deleteTOTPMFA']);
        Route::post('/get-totp-mfa-url', [App\Http\Controllers\Dashboard\UserController::class, 'getTOTPMFAUrl']);
        Route::post('/verify-totp-mfa', [App\Http\Controllers\Dashboard\UserController::class, 'verifyTOTPMFA']);
    });

    Route::prefix('author')->group(function() {
        Route::post('/get-own-profile', [App\Http\Controllers\Dashboard\BlogController::class, 'getProfileOfUser']);

        Route::post('/get-all-own-articles', [App\Http\Controllers\Dashboard\BlogController::class, 'getAllArticlesOfUser']);
        Route::post('/create-article', [App\Http\Controllers\Dashboard\BlogController::class, 'createArticle']);
        Route::post('/update-article', [App\Http\Controllers\Dashboard\BlogController::class, 'updateArticle']);
        Route::post('/set-article-publish-date', [App\Http\Controllers\Dashboard\BlogController::class, 'setPublishDate']);
        Route::post('/delete-article', [App\Http\Controllers\Dashboard\BlogController::class, 'deleteArticle']);
        
        Route::post('/get-all-categories', [App\Http\Controllers\Dashboard\BlogController::class, 'getAllArticleCategories']);
        Route::post('/create-article-category', [App\Http\Controllers\Dashboard\BlogController::class, 'createArticleCategory']);
        Route::post('/update-article-category', [App\Http\Controllers\Dashboard\BlogController::class, 'updateArticleCategory']);
        Route::post('/delete-article-category', [App\Http\Controllers\Dashboard\BlogController::class, 'deleteArticleCategory']);
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

Route::prefix('oauth')->group(function() {
    Route::prefix('google')->group(function() {
        Route::get('/redirect', [App\Http\Controllers\Auth\OauthController::class, 'googleRedirect']);
        Route::get('/callback', [App\Http\Controllers\Auth\OauthController::class, 'callback'])->defaults('provider', 'google')->defaults('type', 'oauth2');
    });

    Route::prefix('facebook')->group(function() {
        Route::get('/redirect', [App\Http\Controllers\Auth\OauthController::class, 'facebookRedirect']);
        Route::get('/callback', [App\Http\Controllers\Auth\OauthController::class, 'callback'])->defaults('provider', 'facebook')->defaults('type', 'oauth2');
    });

    Route::prefix('github')->group(function() {
        Route::get('/redirect', [App\Http\Controllers\Auth\OauthController::class, 'githubRedirect']);
        Route::get('/callback', [App\Http\Controllers\Auth\OauthController::class, 'callback'])->defaults('provider', 'github')->defaults('type', 'oauth2');
    });
});