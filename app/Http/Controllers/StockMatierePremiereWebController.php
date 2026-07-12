<?php

namespace App\Http\Controllers;

use App\Models\MatierePremiere;
use App\Models\Lot;
use App\Models\Magasin;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMatierePremiereWebController extends Controller
{
    public function index(Request $request)
    {
        $query = MatierePremiere::query();

        // Filtrage par magasin
        if ($request->filled('magasin_id')) {
            $magasinId = $request->input('magasin_id');
            $query->whereHas('lots', function ($q) use ($magasinId) {
                $q->where('lot_matiere_premiere.magasin_id', $magasinId);
            });
        }

        // Filtrage par lot
        if ($request->filled('lot_id')) {
            $lotId = $request->input('lot_id');
            $query->whereHas('lots', function ($q) use ($lotId) {
                $q->where('lot_matiere_premiere.lot_id', $lotId);
            });
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $matieres = $query->get()->map(function ($matiere) use ($request) {
            // Lire les stocks via la table pivot (le magasin est désormais dans le pivot)
            $pivotQuery = DB::table('lot_matiere_premiere as lmp')
                ->join('lots as l', 'lmp.lot_id', '=', 'l.id')
                ->join('magasins as m', 'lmp.magasin_id', '=', 'm.id')
                ->where('lmp.matiere_premiere_id', $matiere->id);

            if ($request->filled('magasin_id')) {
                $pivotQuery->where('lmp.magasin_id', $request->input('magasin_id'));
            }
            if ($request->filled('lot_id')) {
                $pivotQuery->where('lmp.lot_id', $request->input('lot_id'));
            }

            $rows = $pivotQuery->select(
                'lmp.magasin_id',
                'm.nom as magasin_nom',
                'l.code_lot',
                'lmp.quantite',
                'lmp.quantite_utiliser'
            )->get();

            $totalQuantite = 0;
            $totalUtilise = 0;
            $parMagasin = [];
            $parLot = [];

            foreach ($rows as $row) {
                $q = $row->quantite ?? 0;
                $u = $row->quantite_utiliser ?? 0;
                $totalQuantite += $q;
                $totalUtilise += $u;

                if (!isset($parMagasin[$row->magasin_id])) {
                    $parMagasin[$row->magasin_id] = ['nom' => $row->magasin_nom, 'quantite' => 0, 'utilise' => 0];
                }
                $parMagasin[$row->magasin_id]['quantite'] += $q;
                $parMagasin[$row->magasin_id]['utilise'] += $u;

                $parLot[] = [
                    'code_lot' => $row->code_lot,
                    'magasin' => $row->magasin_nom,
                    'quantite' => $q,
                    'utilise' => $u,
                    'disponible' => $q - $u,
                ];
            }

            $seuil = $matiere->seuil_alerte ?? 10;
            $matiere->stock_total = $totalQuantite;
            $matiere->stock_utilise = $totalUtilise;
            $matiere->stock_disponible = $totalQuantite - $totalUtilise;
            $matiere->par_magasin = array_values($parMagasin);
            $matiere->par_lot = $parLot;
            $matiere->alerte_stock = ($totalQuantite - $totalUtilise) < $seuil;

            return $matiere;
        });

        return response()->json($matieres);
    }

    public function details($id)
    {
        $matiere = MatierePremiere::findOrFail($id);

        $totalQuantite = 0;
        $totalUtilise = 0;
        $parMagasin = [];
        $parLot = [];

        // Lire les stocks via la table pivot (le magasin est désormais dans le pivot)
        $rows = DB::table('lot_matiere_premiere as lmp')
            ->join('lots as l', 'lmp.lot_id', '=', 'l.id')
            ->join('magasins as m', 'lmp.magasin_id', '=', 'm.id')
            ->where('lmp.matiere_premiere_id', $id)
            ->select('lmp.magasin_id', 'm.nom as magasin_nom', 'l.code_lot', 'lmp.quantite', 'lmp.quantite_utiliser')
            ->get();

        foreach ($rows as $row) {
            $q = $row->quantite ?? 0;
            $u = $row->quantite_utiliser ?? 0;
            $totalQuantite += $q;
            $totalUtilise += $u;

            if (!isset($parMagasin[$row->magasin_id])) {
                $parMagasin[$row->magasin_id] = ['nom' => $row->magasin_nom, 'quantite' => 0, 'utilise' => 0];
            }
            $parMagasin[$row->magasin_id]['quantite'] += $q;
            $parMagasin[$row->magasin_id]['utilise'] += $u;

            $parLot[] = [
                'code_lot' => $row->code_lot,
                'magasin' => $row->magasin_nom,
                'quantite' => $q,
                'utilise' => $u,
                'disponible' => $q - $u,
            ];
        }

        // Historique récent
        $historique = MouvementStock::where('matiere_id', $id)
            ->with('magasin', 'lot')
            ->orderBy('date_mouvement', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'matiere' => $matiere,
            'stock_total' => $totalQuantite,
            'stock_utilise' => $totalUtilise,
            'stock_disponible' => $totalQuantite - $totalUtilise,
            'par_magasin' => $parMagasin,
            'par_lot' => $parLot,
            'historique' => $historique,
        ]);
    }

    public function mouvement(Request $request)
    {
        $request->validate([
            'matiere_id' => 'required|exists:matiere_premieres,id',
            'magasin_id' => 'required|exists:magasins,id',
            'lot_id' => 'required|exists:lots,id',
            'type' => 'required|in:entree,sortie',
            'quantite' => 'required|numeric|min:0.01',
            'observation' => 'nullable|string',
        ]);

        $matiereId = $request->matiere_id;
        $lotId = $request->lot_id;
        $magasinId = $request->magasin_id;
        $quantite = $request->quantite;
        $type = $request->type;

        // Vérifier que la matière est dans le lot avec le magasin spécifique
        $lot = Lot::findOrFail($lotId);
        
        // Trouver l'entrée spécifique dans la table pivot avec le magasin_id
        $pivotEntry = DB::table('lot_matiere_premiere')
            ->where('lot_id', $lotId)
            ->where('matiere_premiere_id', $matiereId)
            ->where('magasin_id', $magasinId)
            ->first();

        // Une sortie exige que la matière existe déjà dans le lot/magasin
        if (!$pivotEntry && $type === 'sortie') {
            return response()->json(['error' => 'Cette matière première n\'est pas dans ce lot pour ce magasin'], 400);
        }

        if ($pivotEntry) {
            $disponible = $pivotEntry->quantite - ($pivotEntry->quantite_utiliser ?? 0);
            if ($type === 'sortie' && $quantite > $disponible) {
                return response()->json(['error' => 'Quantité insuffisante en stock. Disponible: ' . $disponible], 400);
            }
        }

        DB::beginTransaction();
        try {
            // Mettre à jour la quantité dans l'entrée spécifique
            if ($type === 'sortie') {
                DB::table('lot_matiere_premiere')
                    ->where('lot_id', $lotId)
                    ->where('matiere_premiere_id', $matiereId)
                    ->where('magasin_id', $magasinId)
                    ->update([
                        'quantite_utiliser' => ($pivotEntry->quantite_utiliser ?? 0) + $quantite,
                    ]);
            } elseif ($pivotEntry) {
                // Pour une entrée existante, on augmente la quantité totale
                DB::table('lot_matiere_premiere')
                    ->where('lot_id', $lotId)
                    ->where('matiere_premiere_id', $matiereId)
                    ->where('magasin_id', $magasinId)
                    ->update([
                        'quantite' => $pivotEntry->quantite + $quantite,
                    ]);
            } else {
                // Entrée d'une matière non encore présente dans ce lot/magasin : on l'ajoute
                DB::table('lot_matiere_premiere')->insert([
                    'lot_id' => $lotId,
                    'matiere_premiere_id' => $matiereId,
                    'magasin_id' => $magasinId,
                    'quantite' => $quantite,
                    'quantite_utiliser' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Enregistrer le mouvement
            MouvementStock::create([
                'magasin_id' => $magasinId,
                'matiere_id' => $matiereId,
                'lot_id' => $lotId,
                'type' => $type,
                'quantite' => $quantite,
                'date_mouvement' => now(),
                'gerant_id' => auth()->id(),
                'observation' => $request->observation,
            ]);

            DB::commit();

            return response()->json(['message' => 'Mouvement enregistré avec succès']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de l\'enregistrement du mouvement: ' . $e->getMessage()], 500);
        }
    }

    public function historique(Request $request)
    {
        try {
            $query = MouvementStock::with('matiere', 'magasin', 'lot', 'gerant');

            if ($request->filled('matiere_id')) {
                $query->where('matiere_id', $request->matiere_id);
            }

            if ($request->filled('magasin_id')) {
                $query->where('magasin_id', $request->magasin_id);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('date_debut')) {
                $query->where('date_mouvement', '>=', $request->date_debut);
            }

            if ($request->filled('date_fin')) {
                $query->where('date_mouvement', '<=', $request->date_fin);
            }

            $mouvements = $query->orderBy('created_at', 'desc')->paginate(50);

            return response()->json([
                'data' => $mouvements->items(),
                'current_page' => $mouvements->currentPage(),
                'last_page' => $mouvements->lastPage(),
                'total' => $mouvements->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'total' => 0,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function statistiques(Request $request)
    {
        try {
            // Évolution du stock dans le temps
            $evolution = MouvementStock::select(
                DB::raw('DATE(date_mouvement) as date'),
                DB::raw('SUM(CASE WHEN type = "entree" THEN quantite ELSE 0 END) as entree'),
                DB::raw('SUM(CASE WHEN type = "sortie" THEN quantite ELSE 0 END) as sortie')
            )
            ->where('date_mouvement', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(date_mouvement)'))
            ->orderBy('date')
            ->get();

            // Top des matières les plus utilisées
            $topMatieres = MouvementStock::select('matiere_id', DB::raw('SUM(quantite) as total'))
                ->where('type', 'sortie')
                ->where('date_mouvement', '>=', now()->subDays(30))
                ->with('matiere')
                ->groupBy('matiere_id')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            // Alertes de rupture de stock
            $alertes = DB::table('lot_matiere_premiere as lmp')
                ->join('lots as l', 'lmp.lot_id', '=', 'l.id')
                ->join('matiere_premieres as mp', 'lmp.matiere_premiere_id', '=', 'mp.id')
                ->select('mp.id', 'mp.nom', 'mp.code', 'l.code_lot', 'lmp.quantite', 'lmp.quantite_utiliser')
                ->whereRaw('(lmp.quantite - COALESCE(lmp.quantite_utiliser, 0)) < 10')
                ->get()
                ->map(function ($item) {
                    $item->disponible = $item->quantite - ($item->quantite_utiliser ?? 0);
                    return $item;
                });

            return response()->json([
                'evolution' => $evolution,
                'top_matieres' => $topMatieres,
                'alertes' => $alertes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'evolution' => [],
                'top_matieres' => [],
                'alertes' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function storeLot(Request $request)
    {
        $request->validate([
            'matieres' => 'required|array|min:1',
            'matieres.*.matiere_id' => 'required|exists:matiere_premieres,id',
            'matieres.*.quantite' => 'required|numeric|min:0.01',
            'matieres.*.magasin_id' => 'required|exists:magasins,id',
        ]);

        DB::beginTransaction();
        try {
            // Créer le lot
            $lot = Lot::create([
                'code_lot' => Lot::generateCodeLot(),
                'created_by' => auth()->id(),
            ]);

            // Insérer chaque matière première avec son magasin dans la table pivot
            foreach ($request->matieres as $matiereData) {
                DB::table('lot_matiere_premiere')->insert([
                    'lot_id' => $lot->id,
                    'matiere_premiere_id' => $matiereData['matiere_id'],
                    'magasin_id' => $matiereData['magasin_id'],
                    'quantite' => $matiereData['quantite'],
                    'quantite_utiliser' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Créer un mouvement d'entrée automatique
                MouvementStock::create([
                    'magasin_id' => $matiereData['magasin_id'],
                    'matiere_id' => $matiereData['matiere_id'],
                    'lot_id' => $lot->id,
                    'type' => 'entree',
                    'quantite' => $matiereData['quantite'],
                    'date_mouvement' => now(),
                    'gerant_id' => auth()->id(),
                    'observation' => 'Entrée initiale - Création du lot ' . $lot->code_lot,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Lot créé avec succès',
                'lot' => $lot->load('matierePremieres'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de la création du lot: ' . $e->getMessage()], 500);
        }
    }

    public function matiereDetails(Request $request, $id)
    {
        try {
            $matiere = MatierePremiere::findOrFail($id);
            
            // Récupérer les stocks par magasin
            $stocksParMagasin = DB::table('lot_matiere_premiere as lmp')
                ->join('lots as l', 'lmp.lot_id', '=', 'l.id')
                ->join('magasins as m', 'lmp.magasin_id', '=', 'm.id')
                ->select(
                    'm.id as magasin_id',
                    'm.nom as magasin_nom',
                    'l.code_lot',
                    'lmp.quantite',
                    'lmp.quantite_utiliser',
                    DB::raw('(lmp.quantite - COALESCE(lmp.quantite_utiliser, 0)) as disponible')
                )
                ->where('lmp.matiere_premiere_id', $id)
                ->orderBy('m.nom')
                ->get();

            // Grouper par magasin
            $grouped = $stocksParMagasin->groupBy('magasin_id');
            $result = [];
            
            foreach ($grouped as $magasinId => $items) {
                $result[] = [
                    'magasin_id' => $magasinId,
                    'magasin_nom' => $items->first()->magasin_nom,
                    'lots' => $items->map(function($item) {
                        return [
                            'code_lot' => $item->code_lot,
                            'quantite' => $item->quantite,
                            'quantite_utiliser' => $item->quantite_utiliser,
                            'disponible' => $item->disponible,
                        ];
                    })->toArray(),
                    'total_quantite' => $items->sum('quantite'),
                    'total_utilise' => $items->sum('quantite_utiliser'),
                    'total_disponible' => $items->sum('disponible'),
                ];
            }

            return response()->json([
                'matiere' => $matiere,
                'stocks' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getMagasinsForMatiere(Request $request, $matiereId)
    {
        try {
            $magasins = DB::table('lot_matiere_premiere as lmp')
                ->join('magasins as m', 'lmp.magasin_id', '=', 'm.id')
                ->select('m.id', 'm.nom')
                ->where('lmp.matiere_premiere_id', $matiereId)
                ->whereRaw('(lmp.quantite - COALESCE(lmp.quantite_utiliser, 0)) > 0')
                ->distinct()
                ->orderBy('m.nom')
                ->get();

            return response()->json($magasins);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getLotsForMatiereMagasin(Request $request, $matiereId, $magasinId)
    {
        try {
            $lots = DB::table('lot_matiere_premiere as lmp')
                ->join('lots as l', 'lmp.lot_id', '=', 'l.id')
                ->select('l.id', 'l.code_lot', 'lmp.quantite', 'lmp.quantite_utiliser', DB::raw('(lmp.quantite - COALESCE(lmp.quantite_utiliser, 0)) as disponible'))
                ->where('lmp.matiere_premiere_id', $matiereId)
                ->where('lmp.magasin_id', $magasinId)
                ->whereRaw('(lmp.quantite - COALESCE(lmp.quantite_utiliser, 0)) > 0')
                ->orderBy('l.code_lot')
                ->get();

            return response()->json($lots);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAllLots(Request $request)
    {
        try {
            $lots = Lot::with(['createdBy'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($lots);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function lotDetails(Request $request, $lotId)
    {
        try {
            $lot = Lot::with(['createdBy'])->findOrFail($lotId);
            
            // Récupérer les matières premières du lot avec leurs magasins
            $matieres = DB::table('lot_matiere_premiere as lmp')
                ->join('matiere_premieres as mp', 'lmp.matiere_premiere_id', '=', 'mp.id')
                ->join('magasins as m', 'lmp.magasin_id', '=', 'm.id')
                ->select(
                    'mp.id as matiere_id',
                    'mp.nom as matiere_nom',
                    'mp.code as matiere_code',
                    'mp.unite as matiere_unite',
                    'm.id as magasin_id',
                    'm.nom as magasin_nom',
                    'lmp.quantite',
                    'lmp.quantite_utiliser',
                    DB::raw('(lmp.quantite - COALESCE(lmp.quantite_utiliser, 0)) as disponible')
                )
                ->where('lmp.lot_id', $lotId)
                ->orderBy('m.nom')
                ->orderBy('mp.nom')
                ->get();

            return response()->json([
                'lot' => $lot,
                'matieres' => $matieres,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function mouvementDetails(Request $request, $mouvementId)
    {
        try {
            $mouvement = MouvementStock::with(['matiere', 'magasin', 'lot', 'gerant'])->findOrFail($mouvementId);
            return response()->json([
                'mouvement' => $mouvement,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteLot(Request $request, $id)
    {
        try {
            $lot = Lot::findOrFail($id);
            
            // Vérifier les mouvements de ce lot
            $mouvements = MouvementStock::where('lot_id', $id)->get();
            
            // Vérifier s'il y a des sorties
            $hasSorties = $mouvements->where('type', 'sortie')->count() > 0;
            if ($hasSorties) {
                return response()->json(['error' => 'Ce lot ne peut pas être supprimé car il contient des mouvements de sortie.'], 400);
            }
            
            // Vérifier le nombre d'entrées (max 2: création + 1 entrée supplémentaire)
            $entreesCount = $mouvements->where('type', 'entree')->count();
            if ($entreesCount > 2) {
                return response()->json(['error' => 'Ce lot ne peut pas être supprimé car il contient plus de 2 mouvements d\'entrée.'], 400);
            }
            
            // Les cascades sont configurées dans les migrations, donc:
            // - lot_matiere_premiere sera supprimé en cascade
            // - mouvement_stocks sera supprimé en cascade
            
            $lot->delete();
            
            return response()->json(['message' => 'Lot supprimé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression du lot: ' . $e->getMessage()], 500);
        }
    }

    public function deleteMatiereFromLot(Request $request)
    {
        try {
            $request->validate([
                'lot_id' => 'required|exists:lots,id',
                'matiere_id' => 'required|exists:matiere_premieres,id',
                'magasin_id' => 'required|exists:magasins,id',
            ]);

            // Supprimer l'entrée dans la table pivot
            DB::table('lot_matiere_premiere')
                ->where('lot_id', $request->lot_id)
                ->where('matiere_premiere_id', $request->matiere_id)
                ->where('magasin_id', $request->magasin_id)
                ->delete();

            return response()->json(['message' => 'Matière première supprimée du lot avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()], 500);
        }
    }

    public function updateMatiereInLot(Request $request)
    {
        try {
            $request->validate([
                'lot_id' => 'required|exists:lots,id',
                'matiere_id' => 'required|exists:matiere_premieres,id',
                'magasin_id' => 'required|exists:magasins,id',
                'quantite' => 'required|numeric|min:0.01',
            ]);

            // Mettre à jour ou créer l'entrée dans la table pivot
            DB::table('lot_matiere_premiere')
                ->updateOrInsert(
                    [
                        'lot_id' => $request->lot_id,
                        'matiere_premiere_id' => $request->matiere_id,
                        'magasin_id' => $request->magasin_id,
                    ],
                    [
                        'quantite' => DB::raw('quantite + ' . $request->quantite),
                        'updated_at' => now(),
                    ]
                );

            return response()->json(['message' => 'Quantité mise à jour avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()], 500);
        }
    }
}
