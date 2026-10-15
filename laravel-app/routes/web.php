<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\GoogleAuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/ads/create', [App\Http\Controllers\Web\AdController::class, 'create'])->name('ads.create');
    Route::post('/ads', [App\Http\Controllers\Web\AdController::class, 'store'])->name('ads.store');
    Route::get('/ads/{id}/edit', [App\Http\Controllers\Web\AdController::class, 'edit'])->name('ads.edit');
    Route::put('/ads/{id}', [App\Http\Controllers\Web\AdController::class, 'update'])->name('ads.update');
    Route::delete('/ads/{id}', [App\Http\Controllers\Web\AdController::class, 'destroy'])->name('ads.destroy');
    
    Route::post('/ads/{id}/complaint', [App\Http\Controllers\Web\ComplaintController::class, 'store'])->name('ads.complaint');

    Route::get('/my/ads', [App\Http\Controllers\Web\AdController::class, 'myAds'])->name('my.ads');
    Route::get('/my/favorites', [App\Http\Controllers\Web\AdController::class, 'favorites'])->name('my.favorites');
    
    Route::post('/ads/{id}/favorite', [App\Http\Controllers\Web\FavoriteController::class, 'store'])->name('favorites.add');
    Route::delete('/ads/{id}/favorite', [App\Http\Controllers\Web\FavoriteController::class, 'destroy'])->name('favorites.remove');
});

Route::get('/ads', [App\Http\Controllers\Web\AdController::class, 'index'])->name('ads.index');
Route::get('/ads/{id}', [App\Http\Controllers\Web\AdController::class, 'show'])->name('ads.show');

Route::get('/images/{path}', [App\Http\Controllers\Web\AdController::class, 'getImage'])->where('path', '.*')->name('ads.image'); // We need to add this method to Web/AdController or reuse Api one. OR better, add it to Web/AdController now.

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\Web\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

        Route::get('/ads', [App\Http\Controllers\Web\Admin\AdModerationController::class, 'index'])->name('admin.ads.index');
        Route::post('/ads/{id}/approve', [App\Http\Controllers\Web\Admin\AdModerationController::class, 'approve'])->name('admin.ads.approve');
        Route::post('/ads/{id}/reject', [App\Http\Controllers\Web\Admin\AdModerationController::class, 'reject'])->name('admin.ads.reject');

        Route::get('/logs', [App\Http\Controllers\Web\Admin\AdModerationController::class, 'logs'])->name('admin.logs');
    });

Route::redirect('/admin', '/admin/dashboard');

Route::get('/', function () {
    return redirect()->route('ads.index');
});
