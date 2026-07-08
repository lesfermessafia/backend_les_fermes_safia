<?php

namespace App\Http\Controllers;

use App\Models\StockAliment;
use App\Models\HistoriqueAliment;
use Illuminate\Http\Request;

class StockAlimentController extends Controller
{
    public function index()
    {
        $stockAliments = StockAliment::with(['aliment', 'formule', 'historiques'])->get();
        return response()->json($stockAliments);
    }

    public function show($id)
    {
        $stockAliment = StockAliment::with(['aliment', 'formule', 'historiques'])->find($id);
        
        if (!$stockAliment) {
            return response()->json(['error' => 'Stock aliment not found'], 404);
        }
        
        return response()->json($stockAliment);
    }

    public function store(Request $request)
    {
        $request->validate([
            'aliment_id' => 'required|exists:aliments,id',
            'formule_id' => 'required|exists:formules,id',
            'quantite_fabriquer' => 'required|numeric',
            'status' => 'nullable|in:en attente,en production,production terminer,annule,consommer',
        ]);

        $stockAliment = StockAliment::create([
            'aliment_id' => $request->aliment_id,
            'code_stock' => StockAliment::generateCodeStock(),
            'formule_id' => $request->formule_id,
            'quantite_fabriquer' => $request->quantite_fabriquer,
            'status' => $request->status ?? 'en attente',
        ]);

        // Créer automatiquement un historique_aliment avec type entree
        HistoriqueAliment::create([
            'stock_aliment_id' => $stockAliment->id,
            'gerant_id' => auth('api')->id(),
            'type' => 'entree',
            'quantite' => $request->quantite_fabriquer,
            'date_mouvement' => now()->toDateString(),
        ]);

        return response()->json([
            'message' => 'Stock aliment created successfully with entry history',
            'stock_aliment' => $stockAliment->load(['aliment', 'formule', 'historiques'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $stockAliment = StockAliment::find($id);
        
        if (!$stockAliment) {
            return response()->json(['error' => 'Stock aliment not found'], 404);
        }

        $request->validate([
            'aliment_id' => 'nullable|exists:aliments,id',
            'formule_id' => 'nullable|exists:formules,id',
            'quantite_fabriquer' => 'nullable|numeric',
            'status' => 'nullable|in:en attente,en production,production terminer,annule,consommer',
        ]);

        $updateData = [];
        if ($request->has('aliment_id')) {
            $updateData['aliment_id'] = $request->aliment_id;
        }
        if ($request->has('formule_id')) {
            $updateData['formule_id'] = $request->formule_id;
        }
        if ($request->has('quantite_fabriquer')) {
            $updateData['quantite_fabriquer'] = $request->quantite_fabriquer;
        }
        if ($request->has('status')) {
            $updateData['status'] = $request->status;
        }

        $stockAliment->update($updateData);

        return response()->json([
            'message' => 'Stock aliment updated successfully',
            'stock_aliment' => $stockAliment->load(['aliment', 'formule', 'historiques'])
        ]);
    }

    public function destroy($id)
    {
        $stockAliment = StockAliment::find($id);
        
        if (!$stockAliment) {
            return response()->json(['error' => 'Stock aliment not found'], 404);
        }

        $stockAliment->delete();

        return response()->json([
            'message' => 'Stock aliment deleted successfully'
        ]);
    }
}
