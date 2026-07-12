<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EntiteController;
use App\Http\Controllers\MatierePremiereWebController;
use App\Http\Controllers\StockMatierePremiereWebController;
use App\Http\Controllers\AlimentWebController;
use App\Http\Controllers\PouletWebController;
use App\Http\Controllers\FormuleWebController;
use Illuminate\Support\Facades\Storage;

// Route pour servir les images uploadées
Route::get('/img/{filename}', function ($filename) {
    $path = storage_path('app/public/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path);
})->where('filename', '.*');

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

    // Routes de gestion unifiée des entités (Admin uniquement)
    Route::middleware('role:admin')->prefix('admin/entites')->name('admin.entites.')->group(function () {
        Route::get('/', [EntiteController::class, 'index'])->name('index');
        Route::get('/locations', [EntiteController::class, 'getAllLocations'])->name('locations');
        
        // Sites
        Route::prefix('sites')->name('sites.')->group(function () {
            Route::post('/', [EntiteController::class, 'storeSite'])->name('store');
            Route::get('/{id}', [EntiteController::class, 'showSite'])->name('show');
            Route::put('/{id}', [EntiteController::class, 'updateSite'])->name('update');
            Route::delete('/{id}', [EntiteController::class, 'destroySite'])->name('destroy');
        });
        
        // Fermes
        Route::prefix('fermes')->name('fermes.')->group(function () {
            Route::post('/', [EntiteController::class, 'storeFerme'])->name('store');
            Route::get('/{id}', [EntiteController::class, 'showFerme'])->name('show');
            Route::put('/{id}', [EntiteController::class, 'updateFerme'])->name('update');
            Route::delete('/{id}', [EntiteController::class, 'destroyFerme'])->name('destroy');
        });
        
        // Magasins
        Route::prefix('magasins')->name('magasins.')->group(function () {
            Route::post('/', [EntiteController::class, 'storeMagasin'])->name('store');
            Route::get('/{id}', [EntiteController::class, 'showMagasin'])->name('show');
            Route::put('/{id}', [EntiteController::class, 'updateMagasin'])->name('update');
            Route::delete('/{id}', [EntiteController::class, 'destroyMagasin'])->name('destroy');
            Route::get('/all', [EntiteController::class, 'getAllMagasins'])->name('all');
        });
    });

    // Routes de gestion des matières premières (Admin uniquement)
    Route::middleware('role:admin')->prefix('admin/matieres-premieres')->name('admin.matieres-premieres.')->group(function () {
        Route::get('/', [MatierePremiereWebController::class, 'index'])->name('index');
        Route::post('/', [MatierePremiereWebController::class, 'store'])->name('store');
        Route::put('/{id}', [MatierePremiereWebController::class, 'update'])->name('update');
        Route::delete('/{id}', [MatierePremiereWebController::class, 'destroy'])->name('destroy');
        Route::get('/all', [MatierePremiereWebController::class, 'getAll'])->name('all');
        
        // Routes de gestion du stock
        Route::get('/stock', [StockMatierePremiereWebController::class, 'index'])->name('stock.index');
        Route::get('/stock/{id}/details', [StockMatierePremiereWebController::class, 'details'])->name('stock.details');
        Route::get('/stock/matiere/{id}/details', [StockMatierePremiereWebController::class, 'matiereDetails'])->name('stock.matiereDetails');
        Route::get('/stock/matiere/{matiereId}/magasins', [StockMatierePremiereWebController::class, 'getMagasinsForMatiere'])->name('stock.getMagasinsForMatiere');
        Route::get('/stock/matiere/{matiereId}/magasin/{magasinId}/lots', [StockMatierePremiereWebController::class, 'getLotsForMatiereMagasin'])->name('stock.getLotsForMatiereMagasin');
        Route::post('/stock/mouvement', [StockMatierePremiereWebController::class, 'mouvement'])->name('stock.mouvement');
        Route::get('/stock/mouvement/{id}/details', [StockMatierePremiereWebController::class, 'mouvementDetails'])->name('stock.mouvementDetails');
        Route::post('/stock/lot', [StockMatierePremiereWebController::class, 'storeLot'])->name('stock.storeLot');
        Route::get('/stock/historique', [StockMatierePremiereWebController::class, 'historique'])->name('stock.historique');
        Route::get('/stock/lots', [StockMatierePremiereWebController::class, 'getAllLots'])->name('stock.getAllLots');
        Route::get('/stock/lot/{id}/details', [StockMatierePremiereWebController::class, 'lotDetails'])->name('stock.lotDetails');
        Route::delete('/stock/lot/{id}', [StockMatierePremiereWebController::class, 'deleteLot'])->name('stock.deleteLot');
        Route::post('/stock/matiere/delete', [StockMatierePremiereWebController::class, 'deleteMatiereFromLot'])->name('stock.deleteMatiereFromLot');
        Route::post('/stock/matiere/update', [StockMatierePremiereWebController::class, 'updateMatiereInLot'])->name('stock.updateMatiereInLot');
        Route::get('/stock/statistiques', [StockMatierePremiereWebController::class, 'statistiques'])->name('stock.statistiques');
    });

    // Routes de gestion des aliments (Admin uniquement)
    Route::middleware('role:admin')->prefix('admin/aliments')->name('admin.aliments.')->group(function () {
        Route::get('/', [AlimentWebController::class, 'index'])->name('index');
        Route::post('/', [AlimentWebController::class, 'store'])->name('store');
        Route::put('/{id}', [AlimentWebController::class, 'update'])->name('update');
        Route::delete('/{id}', [AlimentWebController::class, 'destroy'])->name('destroy');
        
        // Routes de gestion des stocks d'aliments
        Route::post('/stock', [AlimentWebController::class, 'storeStock'])->name('stock.store');
        Route::put('/stock/{id}', [AlimentWebController::class, 'updateStock'])->name('stock.update');
        Route::delete('/stock/{id}', [AlimentWebController::class, 'destroyStock'])->name('stock.destroy');
        Route::post('/stock/mouvement', [AlimentWebController::class, 'mouvementStock'])->name('stock.mouvement');
        Route::get('/stock/{id}/details', [AlimentWebController::class, 'stockDetails'])->name('stock.details');
    });

    // Routes de gestion des poulets (Admin uniquement)
    Route::middleware('role:admin')->prefix('admin/poulets')->name('admin.poulets.')->group(function () {
        Route::get('/', [PouletWebController::class, 'index'])->name('index');
        Route::post('/', [PouletWebController::class, 'store'])->name('store');
        Route::put('/{id}', [PouletWebController::class, 'update'])->name('update');
        Route::delete('/{id}', [PouletWebController::class, 'destroy'])->name('destroy');
    });

    // Routes de gestion des formules (Admin uniquement)
    Route::middleware('role:admin')->prefix('admin/formules')->name('admin.formules.')->group(function () {
        Route::get('/', [FormuleWebController::class, 'index'])->name('index');
        Route::post('/', [FormuleWebController::class, 'store'])->name('store');
        Route::put('/{id}', [FormuleWebController::class, 'update'])->name('update');
        Route::delete('/{id}', [FormuleWebController::class, 'destroy'])->name('destroy');
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