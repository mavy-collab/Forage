<?php

use App\Http\Controllers\ArdoiseForageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BilanController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\RevenuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenteController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Application (authentifié)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('ventes', VenteController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::resource('clients', ClientController::class);
    Route::resource('depenses', DepenseController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('credits', [CreditController::class, 'index'])->name('credits.index');
    Route::get('credits/{credit}', [CreditController::class, 'show'])->name('credits.show');
    Route::post('credits/{credit}/payer', [CreditController::class, 'payer'])->name('credits.payer');
    Route::get('ardoise', [ArdoiseForageController::class, 'index'])->name('ardoise.index');
    Route::get('ardoise/create', [ArdoiseForageController::class, 'create'])->name('ardoise.create');
    Route::post('ardoise', [ArdoiseForageController::class, 'store'])->name('ardoise.store');
    Route::post('ardoise/{ardoise}/rembourser', [ArdoiseForageController::class, 'rembourser'])->name('ardoise.rembourser');
    Route::delete('ardoise/{ardoise}', [ArdoiseForageController::class, 'destroy'])->name('ardoise.destroy');

    // CSRF refresh pour la synchronisation offline
    Route::get('/api/refresh-csrf', fn() => response()->json(['token' => csrf_token()]))->name('api.csrf');

    // Administration & rapports (admin uniquement)
    Route::middleware('admin')->group(function () {
        Route::get('revenus', [RevenuController::class, 'index'])->name('revenus.index');
        Route::get('journal', [JournalController::class, 'index'])->name('journal.index');
        Route::get('bilan', [BilanController::class, 'index'])->name('bilan.index');
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });
});
