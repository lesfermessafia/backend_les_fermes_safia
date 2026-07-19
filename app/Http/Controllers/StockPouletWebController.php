<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockPoulet;
use App\Models\HistoriqueStockPoulet;
use App\Models\Ferme;
use App\Models\Poulet;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;

class StockPouletWebController extends Controller
{
    public function index(Request $request)
    {
        $query = StockPoulet::with(['ferme', 'poulet']);

        // Filtres
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code_stock', 'like', "%{$search}%")
                  ->orWhereHas('poulet', function ($q) use ($search) {
                      $q->where('nom', 'like', "%{$search}%");
                  });
            });
        }

        if ($fermeId = $request->input('ferme_id')) {
            $query->where('ferme_id', $fermeId);
        }

        if ($statut = $request->input('statut')) {
            $query->where('statut', $statut);
        }

        if ($pouletId = $request->input('poulet_id')) {
            $query->where('poulet_id', $pouletId);
        }

        if ($race = $request->input('race')) {
            $query->whereHas('poulet', function ($q) use ($race) {
                $q->where('race', $race);
            });
        }

        $totalStocks = $query->count();
        $stocks = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        // Statistiques
        $statsByPoulet = (clone StockPoulet::query())
            ->with('poulet')
            ->selectRaw('poulet_id, SUM(quantite) as total')
            ->groupBy('poulet_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->poulet ? $item->poulet->nom : 'Non assigné' => $item->total];
            })
            ->toArray();

        $statsByStatut = (clone StockPoulet::query())
            ->selectRaw('statut, SUM(quantite) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut')
            ->toArray();

        $statsByFerme = (clone StockPoulet::query())
            ->selectRaw('ferme_id, SUM(quantite) as total')
            ->with('ferme')
            ->groupBy('ferme_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->ferme ? $item->ferme->nom : 'Non assigné' => $item->total];
            })
            ->toArray();

        $totalQuantite = StockPoulet::sum('quantite');
        $totalVendus = StockPoulet::where('statut', 'vendu')->sum('quantite');
        $totalReforme = StockPoulet::where('statut', 'Réforme')->sum('quantite');
        $totalNonVendu = StockPoulet::where('statut', 'non vendu')->sum('quantite');
        $totalEnStock = StockPoulet::whereNotIn('statut', StockPoulet::statutsFinaux())->sum('quantite');

        $fermes = Ferme::all();
        $poulets = Poulet::all();
        $races = $poulets->pluck('race')->unique()->filter()->values();

        if ($request->ajax()) {
            return response()->json([
                'stocks' => $stocks->items(),
                'pagination' => $stocks->links()->render(),
                'stats' => [
                    'total' => $totalStocks,
                    'totalQuantite' => $totalQuantite,
                    'byPoulet' => $statsByPoulet,
                    'byStatut' => $statsByStatut,
                    'byFerme' => $statsByFerme,
                    'reforme' => $totalReforme,
                    'nonVendu' => $totalNonVendu,
                    'vendus' => $totalVendus,
                    'enStock' => $totalEnStock,
                ],
            ]);
        }

        return view('pages.admin.gestion-stocks-poulets', compact(
            'stocks',
            'totalStocks',
            'totalQuantite',
            'statsByPoulet',
            'statsByStatut',
            'statsByFerme',
            'totalReforme',
            'totalNonVendu',
            'totalVendus',
            'totalEnStock',
            'fermes',
            'poulets',
            'races'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ferme_id' => 'nullable|exists:fermes,id',
            'poulet_id' => 'required|exists:poulets,id',
            'quantite' => 'required|integer|min:0',
            'date_entree' => 'nullable|date',
            'poids_moyen' => 'nullable|numeric|min:0',
            'age_jours' => 'nullable|integer|min:0',
            'fournisseur' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $poulet = Poulet::find($request->poulet_id);
        $request->validate(['statut' => ['required', 'string', Rule::in(StockPoulet::statutsForPoulet($poulet))]]);

        $stock = StockPoulet::create([
            'ferme_id' => $request->ferme_id,
            'poulet_id' => $request->poulet_id,
            'quantite' => $request->quantite,
            'date_entree' => $request->date_entree,
            'statut' => $request->statut,
            'poids_moyen' => $request->poids_moyen,
            'age_jours' => $request->age_jours,
            'code_stock' => StockPoulet::generateCodeStock(),
            'fournisseur' => $request->fournisseur,
            'notes' => $request->notes,
        ]);

        // Enregistrer le mouvement initial
        if ($request->date_entree) {
            HistoriqueStockPoulet::create([
                'stock_poulet_id' => $stock->id,
                'type_mouvement' => 'entree',
                'quantite' => $request->quantite,
                'motif' => 'Entrée initiale',
                'date_mouvement' => $request->date_entree,
                'notes' => $request->notes,
            ]);
        }

        return redirect()->back()->with('success', 'Stock de poulets créé avec succès');
    }

    public function update(Request $request, $id)
    {
        $stock = StockPoulet::find($id);

        if (!$stock) {
            return redirect()->back()->with('error', 'Stock non trouvé');
        }

        $request->validate([
            'ferme_id' => 'nullable|exists:fermes,id',
            'poulet_id' => 'required|exists:poulets,id',
            'quantite' => 'required|integer|min:0',
            'date_entree' => 'nullable|date',
            'date_sortie' => 'nullable|date',
            'poids_moyen' => 'nullable|numeric|min:0',
            'age_jours' => 'nullable|integer|min:0',
            'fournisseur' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $poulet = Poulet::find($request->poulet_id);
        $request->validate(['statut' => ['required', 'string', Rule::in(StockPoulet::statutsForPoulet($poulet))]]);

        $oldQuantite = $stock->quantite;
        $newQuantite = $request->quantite;

        $stock->update([
            'ferme_id' => $request->ferme_id,
            'poulet_id' => $request->poulet_id,
            'quantite' => $newQuantite,
            'date_entree' => $request->date_entree,
            'date_sortie' => $request->date_sortie,
            'statut' => $request->statut,
            'poids_moyen' => $request->poids_moyen,
            'age_jours' => $request->age_jours,
            'fournisseur' => $request->fournisseur,
            'notes' => $request->notes,
        ]);

        // Enregistrer le mouvement si la quantité a changé
        if ($oldQuantite != $newQuantite) {
            $difference = $newQuantite - $oldQuantite;
            HistoriqueStockPoulet::create([
                'stock_poulet_id' => $stock->id,
                'type_mouvement' => $difference > 0 ? 'entree' : 'sortie',
                'quantite' => abs($difference),
                'motif' => 'Modification de stock',
                'date_mouvement' => now(),
                'notes' => "Ancienne quantité: {$oldQuantite}, Nouvelle quantité: {$newQuantite}",
            ]);
        }

        return redirect()->back()->with('success', 'Stock mis à jour avec succès');
    }

    public function destroy($id)
    {
        $stock = StockPoulet::find($id);

        if (!$stock) {
            return redirect()->back()->with('error', 'Stock non trouvé');
        }

        $stock->delete();

        return redirect()->back()->with('success', 'Stock supprimé avec succès');
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Aucun ID fourni'], 400);
        }

        StockPoulet::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Stocks supprimés avec succès']);
    }

    public function mouvement(Request $request)
    {
        $request->validate([
            'stock_poulet_id' => 'required|exists:stock_poulets,id',
            'type_mouvement' => 'required|in:entree,sortie',
            'quantite' => 'required|integer|min:1',
            'motif' => 'required|string',
            'date_mouvement' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $stock = StockPoulet::find($request->stock_poulet_id);

        if (!$stock) {
            return redirect()->back()->with('error', 'Stock non trouvé');
        }

        // Vérifier si la quantité est suffisante pour une sortie
        if ($request->type_mouvement === 'sortie' && $request->quantite > $stock->quantite) {
            return redirect()->back()->with('error', 'Quantité insuffisante dans le stock');
        }

        // Enregistrer le mouvement
        HistoriqueStockPoulet::create([
            'stock_poulet_id' => $stock->id,
            'type_mouvement' => $request->type_mouvement,
            'quantite' => $request->quantite,
            'motif' => $request->motif,
            'date_mouvement' => $request->date_mouvement,
            'notes' => $request->notes,
        ]);

        // Mettre à jour la quantité du stock
        if ($request->type_mouvement === 'entree') {
            $stock->quantite += $request->quantite;
        } else {
            $stock->quantite -= $request->quantite;
        }

        // Mettre à jour le statut si nécessaire
        if ($request->motif === 'Vente') {
            $stock->statut = ($stock->poulet && $stock->poulet->type === 'pondeuse') ? 'Réforme' : 'vendu';
            $stock->date_sortie = $request->date_mouvement;
        } elseif ($request->motif === 'Mortalité') {
            $stock->statut = 'non vendu';
            $stock->date_sortie = $request->date_mouvement;
        }

        $stock->save();

        return redirect()->back()->with('success', 'Mouvement enregistré avec succès');
    }

    public function historique($id)
    {
        $stock = StockPoulet::with(['ferme', 'historiques'])->find($id);

        if (!$stock) {
            return redirect()->back()->with('error', 'Stock non trouvé');
        }

        return view('pages.admin.historique-stock-poulet', compact('stock'));
    }

    public function changeStatus(Request $request, $id)
    {
        $stock = StockPoulet::with('poulet')->find($id);

        if (!$stock) {
            return redirect()->back()->with('error', 'Stock non trouvé');
        }

        $request->validate([
            'statut' => ['required', 'string', Rule::in(StockPoulet::statutsForPoulet($stock->poulet))],
        ]);

        $stock->statut = $request->statut;

        if (in_array($stock->statut, StockPoulet::statutsFinaux())) {
            $stock->date_sortie = $stock->date_sortie ?? now();
        } else {
            $stock->date_sortie = null;
        }

        $stock->save();

        return redirect()->back()->with('success', 'Statut mis à jour avec succès');
    }
}
