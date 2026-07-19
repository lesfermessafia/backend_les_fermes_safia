<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EntiteController;
use App\Http\Controllers\MatierePremiereWebController;
use App\Http\Controllers\StockMatierePremiereWebController;
use App\Http\Controllers\AlimentWebController;
use App\Http\Controllers\PouletWebController;
use App\Http\Controllers\FormuleWebController;
use App\Http\Controllers\StockPouletWebController;
use App\Http\Controllers\StockOeufWebController;
use App\Http\Controllers\ComptableOeufController;
use App\Http\Controllers\ComptableMatierePremiereController;
use App\Http\Controllers\ComptablePouletController;
use App\Http\Controllers\ComptableFermeMagasinController;
use App\Http\Controllers\ComptableDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\DashboardController;
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

// Mot de passe oublié (code par e-mail)
Route::get('/forgot-password', [ForgotPasswordController::class, 'showEmailForm'])->name('password.forgot');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendCode'])->name('password.send');
Route::get('/forgot-password/code', [ForgotPasswordController::class, 'showCodeForm'])->name('password.code.form');
Route::post('/forgot-password/code', [ForgotPasswordController::class, 'verifyCode'])->name('password.code.verify');
Route::get('/forgot-password/reset', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'reset'])->name('password.reset');

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Profil
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::post('/password/code', [ProfileController::class, 'sendPasswordCode'])->name('password.code');
        Route::post('/password/verify', [ProfileController::class, 'verifyPasswordCode'])->name('password.verify');
    });

    // Dashboard Admin
    Route::get('/admin/dashboard', function () {
        return view('dashboard.admin');
    })->name('admin.dashboard')->middleware('role:admin');

    // Dashboard Statistiques Admin
    Route::middleware('role:admin')->prefix('admin/dashboard-stats')->name('admin.dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('stats');
        Route::get('/data', [DashboardController::class, 'getStats'])->name('stats.data');
    });

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
            Route::get('/{id}/poulets', [EntiteController::class, 'getFermePoulets'])->name('poulets');
        });
        
        // Magasins
        Route::prefix('magasins')->name('magasins.')->group(function () {
            Route::post('/', [EntiteController::class, 'storeMagasin'])->name('store');
            Route::get('/{id}', [EntiteController::class, 'showMagasin'])->name('show');
            Route::put('/{id}', [EntiteController::class, 'updateMagasin'])->name('update');
            Route::delete('/{id}', [EntiteController::class, 'destroyMagasin'])->name('destroy');
            Route::get('/all', [EntiteController::class, 'getAllMagasins'])->name('all');
            Route::get('/{id}/stocks', [EntiteController::class, 'getMagasinStocks'])->name('stocks');
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

    // Routes de gestion des aliments (Comptable)
    Route::middleware('role:comptable')->prefix('comptable/aliments')->name('comptable.aliments.')->group(function () {
        Route::get('/', [AlimentWebController::class, 'comptableIndex'])->name('index');
        Route::post('/mouvement', [AlimentWebController::class, 'mouvementStock'])->name('mouvement');
        Route::put('/stock/{id}/status', [AlimentWebController::class, 'changeStockStatus'])->name('stock.status');
        Route::get('/stock/{id}/details', [AlimentWebController::class, 'stockDetails'])->name('stock.details');
    });

    // Routes de gestion des oeufs (Comptable)
    Route::middleware('role:comptable')->prefix('comptable/oeufs')->name('comptable.oeufs.')->group(function () {
        Route::get('/', [ComptableOeufController::class, 'index'])->name('index');
        Route::post('/', [ComptableOeufController::class, 'store'])->name('store');
        Route::post('/mouvement', [ComptableOeufController::class, 'mouvement'])->name('mouvement');
        Route::get('/stock/{id}/details', [ComptableOeufController::class, 'details'])->name('stock.details');
    });

    // Routes de gestion des matières premières (Comptable)
    Route::middleware('role:comptable')->prefix('comptable/matieres-premieres')->name('comptable.matieres-premieres.')->group(function () {
        Route::get('/', [ComptableMatierePremiereController::class, 'index'])->name('index');
        Route::post('/lot', [ComptableMatierePremiereController::class, 'storeLot'])->name('lot.store');
        Route::post('/mouvement', [ComptableMatierePremiereController::class, 'mouvement'])->name('mouvement');
    });

    // Routes de gestion des poulets (Comptable)
    Route::middleware('role:comptable')->prefix('comptable/poulets')->name('comptable.poulets.')->group(function () {
        Route::get('/', [ComptablePouletController::class, 'index'])->name('index');
        Route::post('/', [ComptablePouletController::class, 'store'])->name('store');
        Route::post('/mouvement', [ComptablePouletController::class, 'mouvement'])->name('mouvement');
        Route::put('/{id}/status', [ComptablePouletController::class, 'changeStatus'])->name('status');
    });

    // Routes de visualisation ferme/magasin (Comptable)
    Route::middleware('role:comptable')->prefix('comptable/fermes-magasins')->name('comptable.fermes-magasins.')->group(function () {
        Route::get('/', [ComptableFermeMagasinController::class, 'index'])->name('index');
    });

    // Routes de gestion des poulets (Admin uniquement)
    Route::middleware('role:admin')->prefix('admin/poulets')->name('admin.poulets.')->group(function () {
        Route::get('/', [PouletWebController::class, 'index'])->name('index');
        Route::post('/', [PouletWebController::class, 'store'])->name('store');
        Route::put('/{id}', [PouletWebController::class, 'update'])->name('update');
        Route::delete('/{id}', [PouletWebController::class, 'destroy'])->name('destroy');
        Route::post('/destroy-multiple', [PouletWebController::class, 'destroyMultiple'])->name('destroyMultiple');

        // Routes de gestion des stocks de poulets
        Route::get('/stocks', [StockPouletWebController::class, 'index'])->name('stocks.index');
        Route::post('/stocks', [StockPouletWebController::class, 'store'])->name('stocks.store');
        Route::put('/stocks/{id}', [StockPouletWebController::class, 'update'])->name('stocks.update');
        Route::delete('/stocks/{id}', [StockPouletWebController::class, 'destroy'])->name('stocks.destroy');
        Route::post('/stocks/destroy-multiple', [StockPouletWebController::class, 'destroyMultiple'])->name('stocks.destroyMultiple');
        Route::post('/stocks/mouvement', [StockPouletWebController::class, 'mouvement'])->name('stocks.mouvement');
        Route::put('/stocks/{id}/change-status', [StockPouletWebController::class, 'changeStatus'])->name('stocks.changeStatus');
        Route::get('/stocks/{id}/historique', [StockPouletWebController::class, 'historique'])->name('stocks.historique');
    });

    // Routes de gestion des formules (Admin uniquement)
    Route::middleware('role:admin')->prefix('admin/formules')->name('admin.formules.')->group(function () {
        Route::get('/', [FormuleWebController::class, 'index'])->name('index');
        Route::post('/', [FormuleWebController::class, 'store'])->name('store');
        Route::put('/{id}', [FormuleWebController::class, 'update'])->name('update');
        Route::delete('/{id}', [FormuleWebController::class, 'destroy'])->name('destroy');
    });

    // Routes de gestion des œufs (Admin uniquement)
    Route::middleware('role:admin')->prefix('admin/oeufs')->name('admin.oeufs.')->group(function () {
        Route::get('/', [StockOeufWebController::class, 'index'])->name('index');
        Route::post('/', [StockOeufWebController::class, 'store'])->name('store');
        Route::put('/{id}', [StockOeufWebController::class, 'update'])->name('update');
        Route::delete('/{id}', [StockOeufWebController::class, 'destroy'])->name('destroy');
        Route::post('/destroy-multiple', [StockOeufWebController::class, 'destroyMultiple'])->name('destroyMultiple');
        Route::post('/mouvement', [StockOeufWebController::class, 'mouvement'])->name('mouvement');
        Route::get('/{id}/historique', [StockOeufWebController::class, 'historique'])->name('historique');
    });

    // Dashboard Comptable
    Route::get('/comptable/dashboard', function () {
        return view('dashboard.comptable');
    })->name('comptable.dashboard')->middleware('role:comptable');

    Route::get('/comptable/tableau-de-bord', [ComptableDashboardController::class, 'index'])
        ->name('comptable.tableau-de-bord')
        ->middleware('role:comptable');

    Route::middleware('adminOrComptable')->prefix('discussion')->name('discussion.')->group(function () {
        Route::get('/', [DiscussionController::class, 'index'])->name('index');
        Route::get('/messages/latest', [DiscussionController::class, 'latest'])->name('messages.latest');
        Route::post('/messages', [DiscussionController::class, 'store'])->name('messages.store');
    });

    Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [NotificationController::class, 'unread'])->name('unread');
        Route::post('/read-all', [NotificationController::class, 'readAll'])->name('read-all');
        Route::get('/{id}/read', [NotificationController::class, 'read'])->name('read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Dashboard Superviseur
    Route::get('/superviseur/dashboard', function () {
        return view('dashboard.superviseur');
    })->name('superviseur.dashboard')->middleware('role:superviseur');
});