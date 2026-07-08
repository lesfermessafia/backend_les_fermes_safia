<?php

namespace App\Http\Controllers;

use App\Models\StockOeuf;
use App\Models\HistoriqueOeuf;
use Illuminate\Http\Request;

class StockOeufController extends Controller
{
    public function index()
    {
        $stockOeufs = StockOeuf::with('historiques')->get();
        return response()->json($stockOeufs);
    }

    public function show($id)
    {
        $stockOeuf = StockOeuf::with(['historiques' => function($query) {
            $query->with('gerant');
        }])->find($id);
        
        if (!$stockOeuf) {
            return response()->json(['error' => 'Stock oeuf not found'], 404);
        }
        
        return response()->json($stockOeuf);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code_ferme' => 'required|string',
            'quantite' => 'required|integer',
            'date_entree' => 'required|date',
        ]);

        $stockOeuf = StockOeuf::create([
            'code_ferme' => $request->code_ferme,
            'quantite' => $request->quantite,
            'date_entree' => $request->date_entree,
        ]);

        // Création automatique d'un historique type entree
        HistoriqueOeuf::create([
            'stock_oeuf_id' => $stockOeuf->id,
            'gerant_id' => auth('api')->id(),
            'type' => 'entree',
            'quantite' => $request->quantite,
            'date_mouvement' => $request->date_entree,
        ]);

        return response()->json([
            'message' => 'Stock oeuf created successfully with historique',
            'stock_oeuf' => $stockOeuf->load('historiques')
        ], 201);
    }
}
