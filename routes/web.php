<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TourismPlaceController;
use App\Http\Controllers\Admin\EventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Umum (Bisa diakses tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/destination', [DestinationController::class, 'index'])->name('all__des');

/*
|--------------------------------------------------------------------------
| Login & Register (Khusus User, Admin dibuat dari Dashboard)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Dashboard & Data Admin (Hanya Admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Wisata
    Route::get('/wisata', [TourismPlaceController::class, 'index'])->name('wisata.index');
    Route::get('/wisata/create', [TourismPlaceController::class, 'create'])->name('wisata.create');
    Route::post('/wisata', [TourismPlaceController::class, 'store'])->name('wisata.store');
    Route::get('/wisata/{id}/edit', [TourismPlaceController::class, 'edit'])->name('wisata.edit');
    Route::put('/wisata/{id}', [TourismPlaceController::class, 'update'])->name('wisata.update');
    Route::patch('/wisata/{id}/status', [TourismPlaceController::class, 'updateStatus'])->name('wisata.updateStatus');
    Route::delete('/wisata/{id}', [TourismPlaceController::class, 'destroy'])->name('wisata.destroy');
    Route::get('/wisata/search', [TourismPlaceController::class, 'search'])->name('wisata.search');

    // Data User
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');
    });

    // EVENTS (plural)
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::get('/create', [EventController::class, 'create'])->name('create');
        Route::post('/', [EventController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [EventController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EventController::class, 'update'])->name('update');
        Route::patch('/{id}/status', [EventController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{id}', [EventController::class, 'destroy'])->name('destroy');
    });

    // ALIAS untuk menu yang mungkin menggunakan 'admin.event'
    Route::get('/event', [EventController::class, 'index'])->name('event');
});

/*
|--------------------------------------------------------------------------
| Lupa Password
|--------------------------------------------------------------------------
*/
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('password.email');
Route::get('/account-confirmation', [ForgotPasswordController::class, 'showAccountConfirmationForm'])->name('account.confirmation');
Route::get('/security-question', [ForgotPasswordController::class, 'showSecurityQuestionForm'])->name('security.question');
Route::post('/verify-answer', [ForgotPasswordController::class, 'verifyAnswer'])->name('verify.answer');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('password.update');
Route::get('/password-success', [ForgotPasswordController::class, 'showSuccessPage'])->name('password.success');
