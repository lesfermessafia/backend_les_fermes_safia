<?php

namespace App\Http\Controllers;

use App\Models\ArrivagePoulet;
use Illuminate\Http\Request;

class ArrivagePouletController extends Controller
{
    public function index()
    {
        $arrivages = ArrivagePoulet::with(['poulet', 'ferme', 'mouvements'])->get();
        return response()->json($arrivages);
    }

    public function show($id)
    {
        $arrivage = ArrivagePoulet::with(['poulet', 'ferme', 'mouvements'])->find($id);
        
        if (!$arrivage) {
            return response()->json(['error' => 'Arrivage poulet not found'], 404);
        }
        
        return response()->json($arrivage);
    }

    public function store(Request $request)
    {
        $request->validate([
            'poulet_id' => 'required|exists:poulets,id',
            'ferme_id' => 'required|exists:fermes,id',
            'quantite' => 'required|integer',
            'nom_fournisseur' => 'required|string',
        ]);

        $arrivage = ArrivagePoulet::create([
            'poulet_id' => $request->poulet_id,
            'ferme_id' => $request->ferme_id,
            'quantite' => $request->quantite,
            'nom_fournisseur' => $request->nom_fournisseur,
            'code' => ArrivagePoulet::generateCode(),
        ]);

        return response()->json([
            'message' => 'Arrivage poulet created successfully',
            'arrivage' => $arrivage->load(['poulet', 'ferme', 'mouvements'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $arrivage = ArrivagePoulet::find($id);
        
        if (!$arrivage) {
            return response()->json(['error' => 'Arrivage poulet not found'], 404);
        }

        $request->validate([
            'poulet_id' => 'nullable|exists:poulets,id',
            'ferme_id' => 'nullable|exists:fermes,id',
            'quantite' => 'nullable|integer',
            'nom_fournisseur' => 'nullable|string',
        ]);

        $updateData = [];
        if ($request->has('poulet_id')) {
            $updateData['poulet_id'] = $request->poulet_id;
        }
        if ($request->has('ferme_id')) {
            $updateData['ferme_id'] = $request->ferme_id;
        }
        if ($request->has('quantite')) {
            $updateData['quantite'] = $request->quantite;
        }
        if ($request->has('nom_fournisseur')) {
            $updateData['nom_fournisseur'] = $request->nom_fournisseur;
        }

        $arrivage->update($updateData);

        return response()->json([
            'message' => 'Arrivage poulet updated successfully',
            'arrivage' => $arrivage->load(['poulet', 'ferme', 'mouvements'])
        ]);
    }

    public function destroy($id)
    {
        $arrivage = ArrivagePoulet::find($id);
        
        if (!$arrivage) {
            return response()->json(['error' => 'Arrivage poulet not found'], 404);
        }

        $arrivage->delete();

        return response()->json([
            'message' => 'Arrivage poulet deleted successfully'
        ]);
    }
}
