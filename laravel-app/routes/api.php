<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AdController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\UploadController;
use App\Http\Controllers\Api\V1\Admin\AdModerationController;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH
    |--------------------------------------------------------------------------
    */
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login',    [AuthController::class, 'login']);
    
    // Google Auth
    Route::get('/auth/google/redirect', [\App\Http\Controllers\Api\V1\GoogleAuthController::class, 'redirect']);
    Route::get('/auth/google/callback', [\App\Http\Controllers\Api\V1\GoogleAuthController::class, 'callback']);

    /*
    |--------------------------------------------------------------------------
    | PUBLIC ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/ads',        [AdController::class, 'index']);
    Route::get('/ads',        [AdController::class, 'index']);
    Route::get('/ads/{id}',   [AdController::class, 'show']);
    // Image Proxy Route
    Route::get('/images/{path}', [AdController::class, 'getImage'])->where('path', '.*');

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES (auth + role:admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum', 'role:admin'])
        ->prefix('admin')
        ->group(function () {

            Route::get('/ads', [AdModerationController::class, 'index']);
            Route::post('/ads/{id}/approve', [AdModerationController::class, 'approve']);
            Route::post('/ads/{id}/reject',  [AdModerationController::class, 'reject']);
            Route::get('/moderation/logs', [AdModerationController::class, 'logs']);
            // test endpoint
            Route::get('/test', function () {
                return \App\Helpers\ApiResponse::success(['status' => 'ok'], 'Admin OK');
            });
        });

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATED USER ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {

        // My ads
        Route::get('/my/ads', [AdController::class, 'myAds']);

        // Uploads
        Route::post('/upload', [UploadController::class, 'upload']);

        // Logout
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Ads CRUD
        Route::post('/ads',        [AdController::class, 'store']);
        Route::put('/ads/{id}',    [AdController::class, 'update']);
        Route::delete('/ads/{id}', [AdController::class, 'destroy']);

        // Favorites
        Route::post('/ads/{id}/favorite',   [FavoriteController::class, 'store']);
        Route::delete('/ads/{id}/favorite', [FavoriteController::class, 'destroy']);
        Route::get('/favorites',            [FavoriteController::class, 'index']);
        Route::get('/my/favorites',         [FavoriteController::class, 'myFavorites']);

        // Messages
        Route::post('/ads/{id}/message', [MessageController::class, 'store']);
        Route::get('/dialogs',           [MessageController::class, 'dialogs']);
        Route::get('/dialogs/{ad_id}/{user_id}', [MessageController::class, 'chat']);
        Route::get('/my/messages',       [MessageController::class, 'myMessages']);

        // Moderation by policy (optional)
        Route::post('/ads/{id}/moderate', [AdController::class, 'moderate'])
            ->middleware('can:moderate');

        // Complaints
        Route::post('/ads/{id}/complaint', [\App\Http\Controllers\Api\V1\ComplaintController::class, 'store']);
    });

    // Admin routes for complaints
    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
        Route::get('/complaints', [\App\Http\Controllers\Api\V1\ComplaintController::class, 'index']);
        Route::put('/complaints/{id}', [\App\Http\Controllers\Api\V1\ComplaintController::class, 'update']);
    });
});

