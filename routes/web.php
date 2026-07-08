<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

    // Dashboard Comptable
    Route::get('/comptable/dashboard', function () {
        return view('dashboard.comptable');
    })->name('comptable.dashboard')->middleware('role:comptable');

    // Dashboard Superviseur
    Route::get('/superviseur/dashboard', function () {
        return view('dashboard.superviseur');
    })->name('superviseur.dashboard')->middleware('role:superviseur');
});
