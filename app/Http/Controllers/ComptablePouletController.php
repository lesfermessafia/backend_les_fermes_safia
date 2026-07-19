<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\StockPoulet;
use App\Models\HistoriqueStockPoulet;
use App\Models\Ferme;
use App\Models\Poulet;
use App\Services\StockNotificationService;

class ComptablePouletController extends Controller
{
    public function index(Request $request)
    {
        $query = StockPoulet::with(['ferme', 'poulet']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code_stock', 'like', "%{$search}%")
                  ->orWhere('fournisseur', 'like', "%{$search}%")
                  ->orWhereHas('poulet', function ($q) use ($search) {
                      $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('race', 'like', "%{$search}%");
                  });
            });
        }

        if ($fermeId = $request->input('ferme_id')) {
            $query->where('ferme_id', $fermeId);
        }

        if ($pouletId = $request->input('poulet_id')) {
            $query->where('poulet_id', $pouletId);
        }

        if ($statut = $request->input('statut')) {
            $query->where('statut', $statut);
        }

        if ($race = $request->input('race')) {
            $query->whereHas('poulet', function ($q) use ($race) {
                $q->where('race', $race);
            });
        }

        $stocks = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $fermes = Ferme::all();
        $poulets = Poulet::all();
        $races = $poulets->pluck('race')->unique()->filter()->values();

        $historiques = HistoriqueStockPoulet::with(['stockPoulet.poulet', 'stockPoulet.ferme'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $totalEnStock = StockPoulet::whereNotIn('statut', StockPoulet::statutsFinaux())->sum('quantite');
        $totalVendus = StockPoulet::where('statut', 'vendu')->sum('quantite');
        $totalReforme = StockPoulet::where('statut', 'Réforme')->sum('quantite');
        $totalNonVendu = StockPoulet::where('statut', 'non vendu')->sum('quantite');

        return view('pages.comptable.gestion-poulets', compact(
            'stocks',
            'fermes',
            'poulets',
            'races',
            'historiques',
            'totalEnStock',
            'totalVendus',
            'totalReforme',
            'totalNonVendu'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ferme_id' => 'nullable|exists:fermes,id',
            'poulet_id' => 'required|exists:poulets,id',
            'quantite' => 'required|integer|min:0',
            'date_entree' => 'required|date',
            'poids_moyen' => 'nullable|numeric|min:0',
            'age_jours' => 'nullable|integer|min:0',
            'fournisseur' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $poulet = Poulet::find($request->poulet_id);
        $request->validate([
            'statut' => ['required', 'string', Rule::in(StockPoulet::statutsForPoulet($poulet))],
        ]);

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

        HistoriqueStockPoulet::create([
            'stock_poulet_id' => $stock->id,
            'type_mouvement' => 'entree',
            'quantite' => $request->quantite,
            'motif' => 'Entree initiale',
            'date_mouvement' => $request->date_entree,
            'notes' => $request->notes,
        ]);

        StockNotificationService::notifyRoles(
            'Nouveau stock de poulets',
            'Un stock de ' . $request->quantite . ' poulet(s) a été créé pour la ferme sélectionnée.',
            'stock',
            route('comptable.poulets.index'),
            'orange'
        );

        return redirect()->back()->with('success', 'Stock de poulets cree avec succes');
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
            return redirect()->back()->with('error', 'Stock non trouve');
        }

        if ($request->type_mouvement === 'sortie' && $request->quantite > $stock->quantite) {
            return redirect()->back()->with('error', 'Quantite insuffisante dans le stock');
        }

        HistoriqueStockPoulet::create([
            'stock_poulet_id' => $stock->id,
            'type_mouvement' => $request->type_mouvement,
            'quantite' => $request->quantite,
            'motif' => $request->motif,
            'date_mouvement' => $request->date_mouvement,
            'notes' => $request->notes,
        ]);

        if ($request->type_mouvement === 'entree') {
            $stock->quantite += $request->quantite;
        } else {
            $stock->quantite -= $request->quantite;
        }

        if ($request->motif === 'Vente') {
            $stock->statut = ($stock->poulet && $stock->poulet->type === 'pondeuse') ? 'Réforme' : 'vendu';
            $stock->date_sortie = $request->date_mouvement;
        } elseif ($request->motif === 'Mortalité') {
            $stock->statut = 'non vendu';
            $stock->date_sortie = $request->date_mouvement;
        }

        $stock->save();

        StockNotificationService::notifyRoles(
            'Mouvement poulets enregistré',
            ucfirst($request->type_mouvement) . ' de ' . $request->quantite . ' poulet(s). Motif : ' . $request->motif . '.',
            'mouvement',
            route('comptable.poulets.index'),
            $request->type_mouvement === 'sortie' ? 'orange' : 'green'
        );

        return redirect()->back()->with('success', 'Mouvement enregistre avec succes');
    }

    public function changeStatus(Request $request, $id)
    {
        $stock = StockPoulet::with('poulet')->find($id);

        if (!$stock) {
            return redirect()->back()->with('error', 'Stock non trouve');
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

        return redirect()->back()->with('success', 'Statut mis a jour avec succes');
    }
}
