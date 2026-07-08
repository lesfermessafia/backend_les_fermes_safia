<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Routes de connexion
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', [AuthController::class, 'webLogin'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout');

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Dashboard Admin
    Route::get('/admin/dashboard', function () {
        return view('dashboard.admin');
    })->name('admin.dashboard')->middleware('role:admin');

    // Routes de gestion des utilisateurs (Admin uniquement)
    Route::middleware('role:admin')->prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-block', [UserController::class, 'toggleBlock'])->name('toggleBlock');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Dashboard Comptable
    Route::get('/comptable/dashboard', function () {
        return view('dashboard.comptable');
    })->name('comptable.dashboard')->middleware('role:comptable');

    // Dashboard Superviseur
    Route::get('/superviseur/dashboard', function () {
        return view('dashboard.superviseur');
    })->name('superviseur.dashboard')->middleware('role:superviseur');
});
