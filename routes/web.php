<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\Admin\DashboardController;
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
| Dashboard Admin (Hanya Admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Dashboard utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dummy route untuk Tourism Place sementara
    Route::get('/places', function () {
        return 'Ini halaman dummy Tourism Place sementara';
    })->name('places.index');

    // Dummy route untuk tambah event (nanti bisa diganti)
    Route::get('/events/create', function () {
        return 'Form tambah event sementara';
    })->name('events.create');
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
