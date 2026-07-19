<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockOeuf;
use App\Models\HistoriqueOeuf;
use App\Models\Ferme;
use App\Services\StockNotificationService;

class ComptableOeufController extends Controller
{
    public function index(Request $request)
    {
        $stocks = StockOeuf::orderBy('id', 'desc')->paginate(10)->withQueryString();
        $fermes = Ferme::orderBy('nom')->get();

        $historiques = HistoriqueOeuf::with(['stockOeuf', 'gerant'])
            ->orderBy('date_mouvement', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $totalQuantite = (int) StockOeuf::sum('quantite');

        return view('pages.comptable.gestion-oeufs', compact(
            'stocks',
            'fermes',
            'historiques',
            'totalQuantite'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code_ferme' => 'required|string|max:20',
            'quantite' => 'required|integer|min:0',
            'date_entree' => 'required|date',
        ]);

        $stock = StockOeuf::create([
            'code_ferme' => $request->code_ferme,
            'quantite' => $request->quantite,
            'date_entree' => $request->date_entree,
        ]);

        HistoriqueOeuf::create([
            'stock_oeuf_id' => $stock->id,
            'gerant_id' => auth()->id(),
            'type' => 'entree',
            'quantite' => $request->quantite,
            'date_mouvement' => $request->date_entree,
        ]);

        StockNotificationService::notifyRoles(
            'Nouveau stock d’œufs',
            'Un stock de ' . $request->quantite . ' tablette(s) a été créé pour la ferme ' . ($stock->code_ferme ?? 'non définie') . '.',
            'stock',
            route('comptable.oeufs.index'),
            'yellow'
        );

        return redirect()->back()->with('success', 'Stock d\'œufs créé avec succès');
    }

    public function mouvement(Request $request)
    {
        $request->validate([
            'stock_oeuf_id' => 'required|exists:stock_oeufs,id',
            'type' => 'required|in:entree,sortie',
            'quantite' => 'required|integer|min:1',
            'date_mouvement' => 'required|date',
        ]);

        $stock = StockOeuf::find($request->stock_oeuf_id);

        if (!$stock) {
            return redirect()->back()->with('error', 'Stock non trouvé');
        }

        if ($request->type === 'sortie' && $request->quantite > $stock->quantite) {
            return redirect()->back()->with('error', 'Quantité insuffisante dans le stock');
        }

        HistoriqueOeuf::create([
            'stock_oeuf_id' => $stock->id,
            'gerant_id' => auth()->id(),
            'type' => $request->type,
            'quantite' => $request->quantite,
            'date_mouvement' => $request->date_mouvement,
        ]);

        if ($request->type === 'entree') {
            $stock->quantite += $request->quantite;
        } else {
            $stock->quantite -= $request->quantite;
        }

        $stock->save();

        StockNotificationService::notifyRoles(
            'Mouvement œufs enregistré',
            ucfirst($request->type) . ' de ' . $request->quantite . ' tablette(s).',
            'mouvement',
            route('comptable.oeufs.index'),
            $request->type === 'sortie' ? 'orange' : 'yellow'
        );

        return redirect()->back()->with('success', 'Mouvement enregistré avec succès');
    }

    public function details($id)
    {
        $stock = StockOeuf::with(['historiques' => function ($query) {
            $query->orderBy('date_mouvement', 'desc')->orderBy('id', 'desc');
        }, 'historiques.gerant'])->find($id);

        if (!$stock) {
            return response()->json(['error' => 'Stock non trouvé'], 404);
        }

        return response()->json([
            'stock' => $stock,
            'historiques' => $stock->historiques,
        ]);
    }
}
