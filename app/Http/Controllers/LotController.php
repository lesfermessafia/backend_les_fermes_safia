<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lot;
use App\Models\MatierePremiere;

class LotController extends Controller
{
    public function index()
    {
        $lots = Lot::with('matierePremieres')->get();
        return response()->json($lots);
    }

    public function show($id)
    {
        $lot = Lot::with('matierePremieres')->find($id);
        
        if (!$lot) {
            return response()->json(['error' => 'Lot not found'], 404);
        }

        // Récupérer les mouvements de stock pour ce lot
        $mouvements = \App\Models\MouvementStock::where('lot_id', $lot->id)->get();
        
        // Calculer les statistiques pour chaque matière première
        $matieresAvecStats = $lot->matierePremieres->map(function($matiere) use ($mouvements) {
            $quantite_initiale = $matiere->pivot->quantite;
            
            $entrees = $mouvements->where('matiere_id', $matiere->id)
                ->where('type', 'entree')
                ->sum('quantite');
            
            $sorties = $mouvements->where('matiere_id', $matiere->id)
                ->where('type', 'sortie')
                ->sum('quantite');
            
            $quantite_restante = $quantite_initiale + $entrees - $sorties;
            
            // Récupérer le dernier mouvement pour obtenir le gérant et le magasin
            $dernierMouvement = $mouvements->where('matiere_id', $matiere->id)->last();
            
            $gerant = null;
            $magasin = null;
            
            if ($dernierMouvement) {
                $gerant = \App\Models\User::find($dernierMouvement->gerant_id);
                $magasin = \App\Models\Magasin::find($dernierMouvement->magasin_id);
            }
            
            return [
                'id' => $matiere->id,
                'nom' => $matiere->nom,
                'code' => $matiere->code,
                'unite' => $matiere->unite,
                'quantite_initiale' => $quantite_initiale,
                'quantite_sortie' => $sorties,
                'quantite_restante' => $quantite_restante,
                'gerant' => $gerant ? [
                    'id' => $gerant->id,
                    'nom' => $gerant->nom,
                    'prenom' => $gerant->prenom,
                    'email' => $gerant->email
                ] : null,
                'magasin' => $magasin ? [
                    'id' => $magasin->id,
                    'nom' => $magasin->nom
                ] : null
            ];
        });
        
        $lot->matierePremieres = $matieresAvecStats;
        
        return response()->json($lot);
    }

    public function showByCode($code_lot)
    {
        $lot = Lot::with('matierePremieres')->where('code_lot', $code_lot)->first();
        
        if (!$lot) {
            return response()->json(['error' => 'Lot not found'], 404);
        }

        // Récupérer les mouvements de stock pour ce lot
        $mouvements = \App\Models\MouvementStock::where('lot_id', $lot->id)->get();
        
        // Calculer les statistiques pour chaque matière première
        $matieresAvecStats = $lot->matierePremieres->map(function($matiere) use ($mouvements) {
            $quantite_initiale = $matiere->pivot->quantite;
            
            $entrees = $mouvements->where('matiere_id', $matiere->id)
                ->where('type', 'entree')
                ->sum('quantite');
            
            $sorties = $mouvements->where('matiere_id', $matiere->id)
                ->where('type', 'sortie')
                ->sum('quantite');
            
            $quantite_restante = $quantite_initiale + $entrees - $sorties;
            
            // Récupérer le dernier mouvement pour obtenir le gérant et le magasin
            $dernierMouvement = $mouvements->where('matiere_id', $matiere->id)->last();
            
            $gerant = null;
            $magasin = null;
            
            if ($dernierMouvement) {
                $gerant = \App\Models\User::find($dernierMouvement->gerant_id);
                $magasin = \App\Models\Magasin::find($dernierMouvement->magasin_id);
            }
            
            return [
                'id' => $matiere->id,
                'nom' => $matiere->nom,
                'code' => $matiere->code,
                'unite' => $matiere->unite,
                'quantite_initiale' => $quantite_initiale,
                'quantite_sortie' => $sorties,
                'quantite_restante' => $quantite_restante,
                'gerant' => $gerant ? [
                    'id' => $gerant->id,
                    'nom' => $gerant->nom,
                    'prenom' => $gerant->prenom,
                    'email' => $gerant->email
                ] : null,
                'magasin' => $magasin ? [
                    'id' => $magasin->id,
                    'nom' => $magasin->nom
                ] : null
            ];
        });
        
        $lot->matierePremieres = $matieresAvecStats;
        
        return response()->json($lot);
    }

    public function store(Request $request)
    {
        $request->validate([
            'magasin_id' => 'required|exists:magasins,id',
            'matieres' => 'required|array',
            'matieres.*.matiere_id' => 'required|exists:matiere_premieres,id',
            'matieres.*.quantite' => 'required|numeric',
        ]);

        $lot = Lot::create([
            'code_lot' => Lot::generateCodeLot(),
            'magasin_id' => $request->magasin_id,
        ]);

        foreach ($request->matieres as $matiere) {
            $lot->matierePremieres()->attach($matiere['matiere_id'], [
                'quantite' => $matiere['quantite']
            ]);

            // Créer automatiquement un mouvement de stock de type entrée
            \App\Models\MouvementStock::create([
                'magasin_id' => $request->magasin_id,
                'matiere_id' => $matiere['matiere_id'],
                'lot_id' => $lot->id,
                'type' => 'entree',
                'quantite' => $matiere['quantite'],
                'date_mouvement' => now()->toDateString(),
                'gerant_id' => auth('api')->id(),
                'observation' => 'Entrée automatique lors de la création du lot',
            ]);
        }

        return response()->json([
            'message' => 'Lot created successfully',
            'lot' => $lot->load(['matierePremieres', 'magasin'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $lot = Lot::find($id);
        
        if (!$lot) {
            return response()->json(['error' => 'Lot not found'], 404);
        }

        $request->validate([
            'matieres' => 'nullable|array',
            'matieres.*.matiere_id' => 'required|exists:matiere_premieres,id',
            'matieres.*.quantite' => 'required|numeric',
        ]);

        if ($request->has('matieres')) {
            $lot->matierePremieres()->detach();

            foreach ($request->matieres as $matiere) {
                $lot->matierePremieres()->attach($matiere['matiere_id'], [
                    'quantite' => $matiere['quantite']
                ]);
            }
        }

        return response()->json([
            'message' => 'Lot updated successfully',
            'lot' => $lot->load('matierePremieres')
        ]);
    }

    public function destroy($id)
    {
        $lot = Lot::find($id);
        
        if (!$lot) {
            return response()->json(['error' => 'Lot not found'], 404);
        }

        $lot->delete();

        return response()->json(['message' => 'Lot deleted successfully']);
    }

    public function updateMatiereInLot(Request $request, $code_lot, $code_matiere)
    {
        $lot = Lot::where('code_lot', $code_lot)->first();
        
        if (!$lot) {
            return response()->json(['error' => 'Lot not found'], 404);
        }

        $matiere = MatierePremiere::where('code', $code_matiere)->first();
        
        if (!$matiere) {
            return response()->json(['error' => 'Matiere premiere not found'], 404);
        }

        $request->validate([
            'quantite' => 'required|numeric',
        ]);

        $lot->matierePremieres()->updateExistingPivot($matiere->id, [
            'quantite' => $request->quantite
        ]);

        return response()->json([
            'message' => 'Matiere premiere updated in lot successfully',
            'lot' => $lot->load('matierePremieres')
        ]);
    }

    public function removeMatiereFromLot($code_lot, $code_matiere)
    {
        $lot = Lot::where('code_lot', $code_lot)->first();
        
        if (!$lot) {
            return response()->json(['error' => 'Lot not found'], 404);
        }

        $matiere = MatierePremiere::where('code', $code_matiere)->first();
        
        if (!$matiere) {
            return response()->json(['error' => 'Matiere premiere not found'], 404);
        }

        $lot->matierePremieres()->detach($matiere->id);

        return response()->json([
            'message' => 'Matiere premiere removed from lot successfully',
            'lot' => $lot->load('matierePremieres')
        ]);
    }
}
