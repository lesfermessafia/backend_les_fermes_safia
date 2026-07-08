<?php

namespace App\Http\Controllers;

use App\Models\HistoriqueOeuf;
use App\Models\StockOeuf;
use Illuminate\Http\Request;

class HistoriqueOeufController extends Controller
{
    public function index()
    {
        $historiques = HistoriqueOeuf::with(['stockOeuf', 'gerant'])->get();
        return response()->json($historiques);
    }

    public function show($id)
    {
        $historique = HistoriqueOeuf::with(['stockOeuf', 'gerant'])->find($id);
        
        if (!$historique) {
            return response()->json(['error' => 'Historique oeuf not found'], 404);
        }
        
        return response()->json($historique);
    }

    public function store(Request $request)
    {
        $request->validate([
            'stock_oeuf_id' => 'required|exists:stock_oeufs,id',
            'type' => 'required|in:entree,sortie,casse,vente',
            'quantite' => 'required|integer',
            'date_mouvement' => 'required|date',
        ]);

        // Validation pour les sorties
        if ($request->type === 'sortie' || $request->type === 'casse' || $request->type === 'vente') {
            $stockOeuf = StockOeuf::find($request->stock_oeuf_id);
            $sortiesTotales = HistoriqueOeuf::where('stock_oeuf_id', $request->stock_oeuf_id)
                ->whereIn('type', ['sortie', 'casse', 'vente'])
                ->sum('quantite');
            $entreesTotales = HistoriqueOeuf::where('stock_oeuf_id', $request->stock_oeuf_id)
                ->where('type', 'entree')
                ->sum('quantite');
            
            $stockActuel = $entreesTotales - $sortiesTotales;
            
            if ($request->quantite > $stockActuel) {
                return response()->json([
                    'error' => 'Quantité insuffisante en stock',
                    'stock_actuel' => $stockActuel,
                    'quantite_demandee' => $request->quantite
                ], 400);
            }
        }

        $historique = HistoriqueOeuf::create([
            'stock_oeuf_id' => $request->stock_oeuf_id,
            'gerant_id' => auth('api')->id(),
            'type' => $request->type,
            'quantite' => $request->quantite,
            'date_mouvement' => $request->date_mouvement,
        ]);

        return response()->json([
            'message' => 'Historique oeuf created successfully',
            'historique' => $historique->load(['stockOeuf', 'gerant'])
        ], 201);
    }
}
