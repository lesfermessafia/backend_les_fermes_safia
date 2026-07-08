<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MatierePremiereController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\FermeController;
use App\Http\Controllers\MagasinController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\MouvementStockController;
use App\Http\Controllers\FormuleController;
use App\Http\Controllers\AlimentController;
use App\Http\Controllers\StockAlimentController;
use App\Http\Controllers\PouletController;
use App\Http\Controllers\ArrivagePouletController;
use App\Http\Controllers\StockOeufController;
use App\Http\Controllers\HistoriqueOeufController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    
    Route::middleware('admin')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::put('/users/{id}/block', [AuthController::class, 'blockUser']);
        Route::get('/users/non-admin', [AuthController::class, 'getUsersNonAdmin']);
        
        // Matiere Premiere routes - create and update only for admin
        Route::post('/matieres', [MatierePremiereController::class, 'store']);
        Route::put('/matieres/{id}', [MatierePremiereController::class, 'update']);
        Route::delete('/matieres/{id}', [MatierePremiereController::class, 'destroy']);
        
        // Site routes - create and update only for admin
        Route::post('/sites', [SiteController::class, 'store']);
        Route::put('/sites/{id}', [SiteController::class, 'update']);
        Route::delete('/sites/{id}', [SiteController::class, 'destroy']);
        
        // Ferme routes - create and update only for admin
        Route::post('/fermes', [FermeController::class, 'store']);
        Route::put('/fermes/{id}', [FermeController::class, 'update']);
        Route::delete('/fermes/{id}', [FermeController::class, 'destroy']);
        
        // Magasin routes - create and update only for admin
        Route::post('/magasins', [MagasinController::class, 'store']);
        Route::put('/magasins/{id}', [MagasinController::class, 'update']);
        Route::delete('/magasins/{id}', [MagasinController::class, 'destroy']);
        
        // Lot routes - create and update only for admin
        Route::post('/lots', [LotController::class, 'store']);
        Route::put('/lots/{id}', [LotController::class, 'update']);
        Route::delete('/lots/{id}', [LotController::class, 'destroy']);
        Route::put('/lots/code/{code_lot}/matiere/{code_matiere}', [LotController::class, 'updateMatiereInLot']);
        Route::delete('/lots/code/{code_lot}/matiere/{code_matiere}', [LotController::class, 'removeMatiereFromLot']);
        
        // Formule routes - create and update only for admin
        Route::post('/formules', [FormuleController::class, 'store']);
        Route::put('/formules/{id}', [FormuleController::class, 'update']);
        Route::delete('/formules/{id}', [FormuleController::class, 'destroy']);
        
        // Aliment routes - create and update only for admin
        Route::post('/aliments', [AlimentController::class, 'store']);
        Route::put('/aliments/{id}', [AlimentController::class, 'update']);
        Route::delete('/aliments/{id}', [AlimentController::class, 'destroy']);
        
        // Stock Aliment routes - create and update only for admin
        Route::post('/stock-aliments', [StockAlimentController::class, 'store']);
        Route::put('/stock-aliments/{id}', [StockAlimentController::class, 'update']);
        Route::delete('/stock-aliments/{id}', [StockAlimentController::class, 'destroy']);
        
        // Poulet routes - create and update only for admin
        Route::post('/poulets', [PouletController::class, 'store']);
        Route::put('/poulets/{id}', [PouletController::class, 'update']);
        Route::delete('/poulets/{id}', [PouletController::class, 'destroy']);
        
        // Arrivage Poulet routes - create and update only for admin
        Route::post('/arrivage-poulets', [ArrivagePouletController::class, 'store']);
        Route::put('/arrivage-poulets/{id}', [ArrivagePouletController::class, 'update']);
        Route::delete('/arrivage-poulets/{id}', [ArrivagePouletController::class, 'destroy']);
        
        // Stock Oeuf routes - create only for admin
        Route::post('/stock-oeufs', [StockOeufController::class, 'store']);
        
        // Historique Oeuf routes - create only for admin
        Route::post('/historique-oeufs', [HistoriqueOeufController::class, 'store']);
    });
    
    // Mouvement Stock routes - accessible to admin and comptable
    Route::middleware('adminOrComptable')->group(function () {
        Route::get('/mouvement-stocks', [MouvementStockController::class, 'index']);
        Route::get('/mouvement-stocks/{id}', [MouvementStockController::class, 'show']);
        Route::post('/mouvement-stocks', [MouvementStockController::class, 'store']);
        Route::put('/mouvement-stocks/{id}', [MouvementStockController::class, 'update']);
        Route::delete('/mouvement-stocks/{id}', [MouvementStockController::class, 'destroy']);
        
        // Mouvement Stock additional routes
        Route::get('/mouvement-stocks/lot/{code_lot}', [MouvementStockController::class, 'getMouvementsByLotCode']);
        Route::get('/mouvement-stocks/lot/{code_lot}/statistics', [MouvementStockController::class, 'getLotStatistics']);
        Route::get('/mouvement-stocks/magasin/{magasin_id}', [MouvementStockController::class, 'getMouvementsByMagasin']);
        Route::get('/mouvement-stocks/magasin/{magasin_id}/statistics', [MouvementStockController::class, 'getMagasinStatistics']);
    });
    
    // Matiere Premiere routes - accessible to all authenticated users
    Route::get('/matieres', [MatierePremiereController::class, 'index']);
    Route::get('/matieres/{id}', [MatierePremiereController::class, 'show']);
    
    // Site routes - accessible to all authenticated users
    Route::get('/sites', [SiteController::class, 'index']);
    Route::get('/sites/{id}', [SiteController::class, 'show']);
    
    // Ferme routes - accessible to all authenticated users
    Route::get('/fermes', [FermeController::class, 'index']);
    Route::get('/fermes/{id}', [FermeController::class, 'show']);
    
    // Magasin routes - accessible to all authenticated users
    Route::get('/magasins', [MagasinController::class, 'index']);
    Route::get('/magasins/{id}', [MagasinController::class, 'show']);
    
    // Lot routes - accessible to all authenticated users
    Route::get('/lots', [LotController::class, 'index']);
    Route::get('/lots/{id}', [LotController::class, 'show']);
    Route::get('/lots/code/{code_lot}', [LotController::class, 'showByCode']);
    
    // Formule routes - accessible to all authenticated users
    Route::get('/formules', [FormuleController::class, 'index']);
    Route::get('/formules/{id}', [FormuleController::class, 'show']);
    
    // Aliment routes - accessible to all authenticated users
    Route::get('/aliments', [AlimentController::class, 'index']);
    Route::get('/aliments/{id}', [AlimentController::class, 'show']);
    
    // Stock Aliment routes - accessible to all authenticated users
    Route::get('/stock-aliments', [StockAlimentController::class, 'index']);
    Route::get('/stock-aliments/{id}', [StockAlimentController::class, 'show']);
    
    // Poulet routes - accessible to all authenticated users
    Route::get('/poulets', [PouletController::class, 'index']);
    Route::get('/poulets/{id}', [PouletController::class, 'show']);
    
    // Arrivage Poulet routes - accessible to all authenticated users
    Route::get('/arrivage-poulets', [ArrivagePouletController::class, 'index']);
    Route::get('/arrivage-poulets/{id}', [ArrivagePouletController::class, 'show']);
    
    // Stock Oeuf routes - accessible to all authenticated users
    Route::get('/stock-oeufs', [StockOeufController::class, 'index']);
    Route::get('/stock-oeufs/{id}', [StockOeufController::class, 'show']);
    
    // Historique Oeuf routes - accessible to all authenticated users
    Route::get('/historique-oeufs', [HistoriqueOeufController::class, 'index']);
    Route::get('/historique-oeufs/{id}', [HistoriqueOeufController::class, 'show']);
});
