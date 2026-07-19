<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Site;
use App\Models\Ferme;
use App\Models\Magasin;
use App\Models\StockPoulet;
use App\Models\MatierePremiere;

class EntiteController extends Controller
{
    public function index(Request $request)
    {
        $allSites = Site::orderBy('nom')->get();
        $search = $request->input('search');
        $siteId = $request->input('site');

        // Sites
        $sitesQuery = Site::with('gerantUser');
        if ($search) {
            $sitesQuery->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('adresse', 'like', "%{$search}%")
                  ->orWhereHas('gerantUser', function ($sub) use ($search) {
                      $sub->where('nom', 'like', "%{$search}%")
                          ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }
        $sites = $sitesQuery->orderBy('id', 'desc')->paginate(10, ['*'], 'sites_page')->withQueryString();

        // Fermes
        $fermesQuery = Ferme::with('site', 'gerantUser');
        if ($search) {
            $fermesQuery->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhereHas('site', function ($sub) use ($search) {
                      $sub->where('nom', 'like', "%{$search}%");
                  })
                  ->orWhereHas('gerantUser', function ($sub) use ($search) {
                      $sub->where('nom', 'like', "%{$search}%")
                          ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }
        if ($siteId) {
            $fermesQuery->where('idsite', $siteId);
        }
        $fermes = $fermesQuery->orderBy('id', 'desc')->paginate(10, ['*'], 'fermes_page')->withQueryString();

        // Magasins
        $magasinsQuery = Magasin::with('site', 'gerantUser');
        if ($search) {
            $magasinsQuery->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhereHas('site', function ($sub) use ($search) {
                      $sub->where('nom', 'like', "%{$search}%");
                  })
                  ->orWhereHas('gerantUser', function ($sub) use ($search) {
                      $sub->where('nom', 'like', "%{$search}%")
                          ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }
        if ($siteId) {
            $magasinsQuery->where('idsite', $siteId);
        }
        $magasins = $magasinsQuery->orderBy('id', 'desc')->paginate(10, ['*'], 'magasins_page')->withQueryString();

        $stats = [
            'totalSites' => $sitesQuery->count(),
            'totalFermes' => $fermesQuery->count(),
            'totalMagasins' => $magasinsQuery->count(),
            'fermesBySite' => Ferme::selectRaw('idsite, COUNT(*) as total')
                ->groupBy('idsite')
                ->get()
                ->mapWithKeys(function ($item) use ($allSites) {
                    $site = $allSites->firstWhere('id', $item->idsite);
                    return [($site ? $site->nom : 'Site #' . $item->idsite) => $item->total];
                })
                ->toArray(),
            'magasinsBySite' => Magasin::selectRaw('idsite, COUNT(*) as total')
                ->groupBy('idsite')
                ->get()
                ->mapWithKeys(function ($item) use ($allSites) {
                    $site = $allSites->firstWhere('id', $item->idsite);
                    return [($site ? $site->nom : 'Site #' . $item->idsite) => $item->total];
                })
                ->toArray(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'sites' => [
                    'items' => $sites->map(function ($site) {
                        return [
                            'id' => $site->id,
                            'nom' => $site->nom,
                            'adresse' => $site->adresse,
                            'latitude' => $site->latitude,
                            'longitude' => $site->longitude,
                            'longueur' => $site->longueur,
                            'largeur' => $site->largeur,
                            'gerant' => $site->gerantUser ? $site->gerantUser->nom . ' ' . $site->gerantUser->prenom : null,
                        ];
                    })->all(),
                    'pagination' => $sites->links()->render(),
                ],
                'fermes' => [
                    'items' => $fermes->map(function ($ferme) {
                        return [
                            'id' => $ferme->id,
                            'nom' => $ferme->nom,
                            'site' => $ferme->site ? $ferme->site->nom : null,
                            'latitude' => $ferme->latitude,
                            'longitude' => $ferme->longitude,
                            'longueur' => $ferme->longueur,
                            'largeur' => $ferme->largeur,
                            'gerant' => $ferme->gerantUser ? $ferme->gerantUser->nom . ' ' . $ferme->gerantUser->prenom : null,
                        ];
                    })->all(),
                    'pagination' => $fermes->links()->render(),
                ],
                'magasins' => [
                    'items' => $magasins->map(function ($magasin) {
                        return [
                            'id' => $magasin->id,
                            'nom' => $magasin->nom,
                            'site' => $magasin->site ? $magasin->site->nom : null,
                            'latitude' => $magasin->latitude,
                            'longitude' => $magasin->longitude,
                            'longueur' => $magasin->longueur,
                            'largeur' => $magasin->largeur,
                            'gerant' => $magasin->gerantUser ? $magasin->gerantUser->nom . ' ' . $magasin->gerantUser->prenom : null,
                        ];
                    })->all(),
                    'pagination' => $magasins->links()->render(),
                ],
                'stats' => $stats,
            ]);
        }

        return view('pages.admin.gestion-entites', compact('allSites', 'sites', 'fermes', 'magasins', 'stats'));
    }

    public function getAllLocations()
    {
        $sites = Site::with('gerantUser')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function($site) {
                return [
                    'id' => $site->id,
                    'nom' => $site->nom,
                    'type' => 'site',
                    'latitude' => $site->latitude,
                    'longitude' => $site->longitude,
                    'site' => null,
                    'gerant' => $site->gerantUser ? $site->gerantUser->nom . ' ' . $site->gerantUser->prenom : null,
                ];
            });

        $fermes = Ferme::with('site', 'gerantUser')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function($ferme) {
                return [
                    'id' => $ferme->id,
                    'nom' => $ferme->nom,
                    'type' => 'ferme',
                    'latitude' => $ferme->latitude,
                    'longitude' => $ferme->longitude,
                    'site' => $ferme->site ? $ferme->site->nom : null,
                    'gerant' => $ferme->gerantUser ? $ferme->gerantUser->nom . ' ' . $ferme->gerantUser->prenom : null,
                ];
            });

        $magasins = Magasin::with('site', 'gerantUser')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function($magasin) {
                return [
                    'id' => $magasin->id,
                    'nom' => $magasin->nom,
                    'type' => 'magasin',
                    'latitude' => $magasin->latitude,
                    'longitude' => $magasin->longitude,
                    'site' => $magasin->site ? $magasin->site->nom : null,
                    'gerant' => $magasin->gerantUser ? $magasin->gerantUser->nom . ' ' . $magasin->gerantUser->prenom : null,
                ];
            });

        $locations = $sites->concat($fermes)->concat($magasins);

        return response()->json($locations);
    }

    // Sites
    public function storeSite(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'adresse' => 'nullable|string',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'longueur' => 'nullable|numeric',
            'largeur' => 'nullable|numeric',
            'gerant' => 'nullable|exists:users,id',
        ]);

        Site::create([
            'nom' => $request->nom,
            'adresse' => $request->adresse,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'longueur' => $request->longueur,
            'largeur' => $request->largeur,
            'gerant' => $request->gerant,
        ]);

        return redirect()->back()->with('success', 'Site créé avec succès');
    }

    public function updateSite(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string',
            'adresse' => 'nullable|string',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'longueur' => 'nullable|numeric',
            'largeur' => 'nullable|numeric',
            'gerant' => 'nullable|exists:users,id',
        ]);

        $site = Site::find($id);
        if (!$site) {
            return response()->json(['error' => 'Site non trouvé'], 404);
        }

        $site->update([
            'nom' => $request->nom,
            'adresse' => $request->adresse,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'longueur' => $request->longueur,
            'largeur' => $request->largeur,
            'gerant' => $request->gerant,
        ]);

        return redirect()->back()->with('success', 'Site mis à jour avec succès');
    }

    public function showSite($id)
    {
        $site = Site::with('gerantUser')->find($id);
        
        if (!$site) {
            return response()->json(['error' => 'Site non trouvé'], 404);
        }

        return response()->json($site);
    }

    public function destroySite($id)
    {
        $site = Site::find($id);
        
        if (!$site) {
            return response()->json(['error' => 'Site non trouvé'], 404);
        }

        $site->delete();

        return response()->json(['message' => 'Site supprimé avec succès']);
    }

    // Fermes
    public function storeFerme(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'idsite' => 'required|exists:sites,id',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'longueur' => 'nullable|numeric',
            'largeur' => 'nullable|numeric',
            'gerant' => 'nullable|exists:users,id',
        ]);

        Ferme::create([
            'nom' => $request->nom,
            'idsite' => $request->idsite,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'longueur' => $request->longueur,
            'largeur' => $request->largeur,
            'gerant' => $request->gerant,
        ]);

        return redirect()->back()->with('success', 'Ferme créée avec succès');
    }

    public function updateFerme(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string',
            'idsite' => 'required|exists:sites,id',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'longueur' => 'nullable|numeric',
            'largeur' => 'nullable|numeric',
            'gerant' => 'nullable|exists:users,id',
        ]);

        $ferme = Ferme::find($id);
        if (!$ferme) {
            return response()->json(['error' => 'Ferme non trouvée'], 404);
        }

        $ferme->update([
            'nom' => $request->nom,
            'idsite' => $request->idsite,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'longueur' => $request->longueur,
            'largeur' => $request->largeur,
            'gerant' => $request->gerant,
        ]);

        return redirect()->back()->with('success', 'Ferme mise à jour avec succès');
    }

    public function showFerme($id)
    {
        $ferme = Ferme::with('site')->find($id);
        
        if (!$ferme) {
            return response()->json(['error' => 'Ferme non trouvée'], 404);
        }

        return response()->json($ferme);
    }

    public function destroyFerme($id)
    {
        $ferme = Ferme::find($id);

        if (!$ferme) {
            return response()->json(['error' => 'Ferme non trouvée'], 404);
        }

        $ferme->delete();

        return response()->json(['message' => 'Ferme supprimée avec succès']);
    }

    public function getFermePoulets($id)
    {
        $ferme = Ferme::find($id);

        if (!$ferme) {
            return response()->json(['error' => 'Ferme non trouvée'], 404);
        }

        // Poulets actuellement dans la ferme (hors statuts finaux)
        $currentStocks = StockPoulet::with('poulet')
            ->where('ferme_id', $id)
            ->whereNotIn('statut', \App\Models\StockPoulet::statutsFinaux())
            ->get()
            ->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'code_stock' => $stock->code_stock,
                    'poulet' => $stock->poulet ? [
                        'id' => $stock->poulet->id,
                        'nom' => $stock->poulet->nom,
                        'race' => $stock->poulet->race,
                    ] : null,
                    'quantite' => $stock->quantite,
                    'statut' => $stock->statut,
                    'date_entree' => $stock->date_entree ? $stock->date_entree->format('d/m/Y') : null,
                ];
            });

        // Historique des poulets (statuts finaux avec date_sortie)
        $historiqueStocks = StockPoulet::with('poulet')
            ->where('ferme_id', $id)
            ->whereIn('statut', \App\Models\StockPoulet::statutsFinaux())
            ->whereNotNull('date_sortie')
            ->orderBy('date_sortie', 'desc')
            ->get()
            ->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'code_stock' => $stock->code_stock,
                    'poulet' => $stock->poulet ? [
                        'id' => $stock->poulet->id,
                        'nom' => $stock->poulet->nom,
                        'race' => $stock->poulet->race,
                    ] : null,
                    'quantite' => $stock->quantite,
                    'statut' => $stock->statut,
                    'date_sortie' => $stock->date_sortie ? $stock->date_sortie->format('d/m/Y') : null,
                ];
            });

        return response()->json([
            'current' => $currentStocks,
            'historique' => $historiqueStocks,
        ]);
    }

    // Magasins
    public function storeMagasin(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'idsite' => 'required|exists:sites,id',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'longueur' => 'nullable|numeric',
            'largeur' => 'nullable|numeric',
            'gerant' => 'nullable|exists:users,id',
        ]);

        Magasin::create([
            'nom' => $request->nom,
            'idsite' => $request->idsite,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'longueur' => $request->longueur,
            'largeur' => $request->largeur,
            'gerant' => $request->gerant,
        ]);

        return redirect()->back()->with('success', 'Magasin créé avec succès');
    }

    public function updateMagasin(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string',
            'idsite' => 'required|exists:sites,id',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'longueur' => 'nullable|numeric',
            'largeur' => 'nullable|numeric',
            'gerant' => 'nullable|exists:users,id',
        ]);

        $magasin = Magasin::find($id);
        if (!$magasin) {
            return response()->json(['error' => 'Magasin non trouvé'], 404);
        }

        $magasin->update([
            'nom' => $request->nom,
            'idsite' => $request->idsite,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'longueur' => $request->longueur,
            'largeur' => $request->largeur,
            'gerant' => $request->gerant,
        ]);

        return redirect()->back()->with('success', 'Magasin mis à jour avec succès');
    }

    public function showMagasin($id)
    {
        $magasin = Magasin::with('site')->find($id);
        
        if (!$magasin) {
            return response()->json(['error' => 'Magasin non trouvé'], 404);
        }

        return response()->json($magasin);
    }

    public function destroyMagasin($id)
    {
        $magasin = Magasin::find($id);
        
        if (!$magasin) {
            return response()->json(['error' => 'Magasin non trouvé'], 404);
        }

        $magasin->delete();

        return response()->json(['message' => 'Magasin supprimé avec succès']);
    }

    public function getAllMagasins()
    {
        $magasins = Magasin::select('id', 'nom')->get();
        return response()->json($magasins);
    }

    public function getMagasinStocks($id)
    {
        $magasin = Magasin::find($id);

        if (!$magasin) {
            return response()->json(['error' => 'Magasin non trouvé'], 404);
        }

        // Récupérer les matières premières du magasin via la table pivot lot_matiere_premiere
        $stocks = MatierePremiere::whereHas('lots', function ($query) use ($id) {
            $query->where('lot_matiere_premiere.magasin_id', $id);
        })
        ->with(['lots' => function ($query) use ($id) {
            $query->where('lot_matiere_premiere.magasin_id', $id);
        }])
        ->get()
        ->map(function ($matiere) use ($id) {
            $pivot = $matiere->lots->first()?->pivot;
            return [
                'id' => $matiere->id,
                'code' => $matiere->code,
                'nom' => $matiere->nom,
                'unite' => $matiere->unite,
                'seuil_alerte' => $matiere->seuil_alerte,
                'quantite' => $pivot ? $pivot->quantite : 0,
                'quantite_utiliser' => $pivot ? $pivot->quantite_utiliser : 0,
            ];
        });

        return response()->json($stocks);
    }
}
