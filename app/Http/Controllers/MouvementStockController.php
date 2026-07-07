<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MouvementStock;
use App\Models\Lot;

class MouvementStockController extends Controller
{
    public function index()
    {
        $mouvements = MouvementStock::with(['magasin', 'matierePremiere', 'lot', 'gerant'])->get();
        return response()->json($mouvements);
    }

    public function show($id)
    {
        $mouvement = MouvementStock::with(['magasin', 'matierePremiere', 'lot', 'gerant'])->find($id);
        
        if (!$mouvement) {
            return response()->json(['error' => 'Mouvement stock not found'], 404);
        }
        
        return response()->json($mouvement);
    }

    public function store(Request $request)
    {
        $request->validate([
            'magasin_id' => 'required|exists:magasins,id',
            'matiere_id' => 'required|exists:matiere_premieres,id',
            'lot_id' => 'required|exists:lots,id',
            'type' => 'required|in:sortie,entree',
            'quantite' => 'required|numeric',
            'date_mouvement' => 'required|date',
            'observation' => 'nullable|string',
        ]);

        // Vérifier le stock disponible pour les sorties
        if ($request->type === 'sortie') {
            $lot = Lot::find($request->lot_id);
            
            // Récupérer la quantité initiale depuis la table pivot
            $lot->load('matierePremieres');
            $matiereInLot = $lot->matierePremieres->where('id', $request->matiere_id)->first();
            
            if (!$matiereInLot) {
                return response()->json(['error' => 'Cette matière première n\'existe pas dans ce lot'], 400);
            }
            
            $quantite_initiale = $matiereInLot->pivot->quantite;
            
            // Calculer les mouvements existants
            $mouvements = MouvementStock::where('lot_id', $request->lot_id)
                ->where('matiere_id', $request->matiere_id)
                ->get();
            
            $entrees = $mouvements->where('type', 'entree')->sum('quantite');
            $sorties = $mouvements->where('type', 'sortie')->sum('quantite');
            
            $stock_actuel = $quantite_initiale + $entrees - $sorties;
            
            if ($request->quantite > $stock_actuel) {
                return response()->json([
                    'error' => 'Quantité insuffisante',
                    'stock_disponible' => $stock_actuel,
                    'quantite_demandee' => $request->quantite
                ], 400);
            }
        }

        $mouvement = MouvementStock::create([
            'magasin_id' => $request->magasin_id,
            'matiere_id' => $request->matiere_id,
            'lot_id' => $request->lot_id,
            'type' => $request->type,
            'quantite' => $request->quantite,
            'date_mouvement' => $request->date_mouvement,
            'gerant_id' => auth('api')->id(),
            'observation' => $request->observation,
        ]);

        return response()->json([
            'message' => 'Mouvement stock created successfully',
            'mouvement' => $mouvement->load(['magasin', 'matierePremiere', 'lot', 'gerant'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $mouvement = MouvementStock::find($id);
        
        if (!$mouvement) {
            return response()->json(['error' => 'Mouvement stock not found'], 404);
        }

        $request->validate([
            'magasin_id' => 'nullable|exists:magasins,id',
            'matiere_id' => 'nullable|exists:matiere_premieres,id',
            'lot_id' => 'nullable|exists:lots,id',
            'type' => 'nullable|in:sortie,entree',
            'quantite' => 'nullable|numeric',
            'date_mouvement' => 'nullable|date',
            'observation' => 'nullable|string',
        ]);

        // Vérifier le stock disponible si on modifie vers une sortie ou si on augmente la quantité d'une sortie
        $newType = $request->has('type') ? $request->type : $mouvement->type;
        $newQuantite = $request->has('quantite') ? $request->quantite : $mouvement->quantite;
        $newLotId = $request->has('lot_id') ? $request->lot_id : $mouvement->lot_id;
        $newMatiereId = $request->has('matiere_id') ? $request->matiere_id : $mouvement->matiere_id;

        if ($newType === 'sortie') {
            $lot = Lot::find($newLotId);
            
            $lot->load('matierePremieres');
            $matiereInLot = $lot->matierePremieres->where('id', $newMatiereId)->first();
            
            if (!$matiereInLot) {
                return response()->json(['error' => 'Cette matière première n\'existe pas dans ce lot'], 400);
            }
            
            $quantite_initiale = $matiereInLot->pivot->quantite;
            
            // Calculer les mouvements existants (sans le mouvement actuel)
            $mouvements = MouvementStock::where('lot_id', $newLotId)
                ->where('matiere_id', $newMatiereId)
                ->where('id', '!=', $id)
                ->get();
            
            $entrees = $mouvements->where('type', 'entree')->sum('quantite');
            $sorties = $mouvements->where('type', 'sortie')->sum('quantite');
            
            $stock_actuel = $quantite_initiale + $entrees - $sorties;
            
            if ($newQuantite > $stock_actuel) {
                return response()->json([
                    'error' => 'Quantité insuffisante',
                    'stock_disponible' => $stock_actuel,
                    'quantite_demandee' => $newQuantite
                ], 400);
            }
        }

        $updateData = [];
        if ($request->has('magasin_id')) {
            $updateData['magasin_id'] = $request->magasin_id;
        }
        if ($request->has('matiere_id')) {
            $updateData['matiere_id'] = $request->matiere_id;
        }
        if ($request->has('lot_id')) {
            $updateData['lot_id'] = $request->lot_id;
        }
        if ($request->has('type')) {
            $updateData['type'] = $request->type;
        }
        if ($request->has('quantite')) {
            $updateData['quantite'] = $request->quantite;
        }
        if ($request->has('date_mouvement')) {
            $updateData['date_mouvement'] = $request->date_mouvement;
        }
        // Utiliser l'utilisateur connecté comme gérant
        $updateData['gerant_id'] = auth('api')->id();
        if ($request->has('observation')) {
            $updateData['observation'] = $request->observation;
        }

        $mouvement->update($updateData);

        return response()->json([
            'message' => 'Mouvement stock updated successfully',
            'mouvement' => $mouvement->load(['magasin', 'matierePremiere', 'lot', 'gerant'])
        ]);
    }

    public function destroy($id)
    {
        $mouvement = MouvementStock::find($id);
        
        if (!$mouvement) {
            return response()->json(['error' => 'Mouvement stock not found'], 404);
        }

        $mouvement->delete();

        return response()->json(['message' => 'Mouvement stock deleted successfully']);
    }

    public function getMouvementsByLotCode($code_lot)
    {
        $lot = Lot::where('code_lot', $code_lot)->first();
        
        if (!$lot) {
            return response()->json(['error' => 'Lot not found'], 404);
        }

        $mouvements = MouvementStock::with(['magasin', 'matierePremiere', 'gerant'])
            ->where('lot_id', $lot->id)
            ->orderBy('date_mouvement', 'desc')
            ->get();

        return response()->json([
            'lot' => $lot->load('matierePremieres'),
            'mouvements' => $mouvements
        ]);
    }

    public function getLotStatistics($code_lot)
    {
        $lot = Lot::where('code_lot', $code_lot)->first();
        
        if (!$lot) {
            return response()->json(['error' => 'Lot not found'], 404);
        }

        $mouvements = MouvementStock::where('lot_id', $lot->id)->get();
        
        $statistics = [];
        
        foreach ($lot->matierePremieres as $matiere) {
            $quantite_initiale = $matiere->pivot->quantite;
            
            $entrees = $mouvements->where('matiere_id', $matiere->id)
                ->where('type', 'entree')
                ->sum('quantite');
            
            $sorties = $mouvements->where('matiere_id', $matiere->id)
                ->where('type', 'sortie')
                ->sum('quantite');
            
            $difference_mouvements = $entrees - $sorties;
            $stock_actuel = $quantite_initiale + $difference_mouvements;
            
            $statistics[] = [
                'matiere_premiere' => $matiere,
                'quantite_initiale' => $quantite_initiale,
                'somme_entree' => $entrees,
                'somme_sortie' => $sorties,
                'difference_mouvements' => $difference_mouvements,
                'stock_actuel' => $stock_actuel
            ];
        }

        return response()->json([
            'lot' => $lot,
            'statistics' => $statistics
        ]);
    }

    public function getMouvementsByMagasin($magasin_id)
    {
        $mouvements = MouvementStock::with(['matierePremiere', 'lot', 'gerant'])
            ->where('magasin_id', $magasin_id)
            ->orderBy('date_mouvement', 'desc')
            ->get();

        return response()->json($mouvements);
    }

    public function getMagasinStatistics($magasin_id)
    {
        $mouvements = MouvementStock::where('magasin_id', $magasin_id)->get();
        
        $statistics = [];
        
        $matieres = $mouvements->pluck('matiere_id')->unique();
        
        foreach ($matieres as $matiere_id) {
            $entrees = $mouvements->where('matiere_id', $matiere_id)
                ->where('type', 'entree')
                ->sum('quantite');
            
            $sorties = $mouvements->where('matiere_id', $matiere_id)
                ->where('type', 'sortie')
                ->sum('quantite');
            
            $difference = $entrees - $sorties;
            
            $matiere = \App\Models\MatierePremiere::find($matiere_id);
            
            $statistics[] = [
                'matiere_premiere' => $matiere,
                'somme_entree' => $entrees,
                'somme_sortie' => $sorties,
                'difference' => $difference,
                'stock_actuel' => $difference
            ];
        }

        return response()->json($statistics);
    }
}
