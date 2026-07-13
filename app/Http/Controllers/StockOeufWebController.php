<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockOeuf;
use App\Models\HistoriqueOeuf;
use App\Models\Ferme;

class StockOeufWebController extends Controller
{
    public function index(Request $request)
    {
        $query = StockOeuf::query();

        if ($search = $request->input('search')) {
            $query->where('code_ferme', 'like', "%{$search}%");
        }

        if ($dateDebut = $request->input('date_debut')) {
            $query->whereDate('date_entree', '>=', $dateDebut);
        }

        if ($dateFin = $request->input('date_fin')) {
            $query->whereDate('date_entree', '<=', $dateFin);
        }

        $totalStocks = $query->count();
        $stocks = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        // Statistiques
        $statsByFerme = (clone StockOeuf::query())
            ->selectRaw('code_ferme, SUM(quantite) as total')
            ->groupBy('code_ferme')
            ->pluck('total', 'code_ferme')
            ->toArray();

        $statsByType = (clone HistoriqueOeuf::query())
            ->selectRaw('type, SUM(quantite) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        $totalQuantite = (int) StockOeuf::sum('quantite');
        $totalOeufs = $totalQuantite * StockOeuf::OEUFS_PAR_TABLETTE;

        $fermes = Ferme::all();

        if ($request->ajax()) {
            return response()->json([
                'stocks' => $stocks->items(),
                'pagination' => $stocks->links()->render(),
                'stats' => [
                    'total' => $totalStocks,
                    'totalQuantite' => $totalQuantite,
                    'totalOeufs' => $totalOeufs,
                    'byFerme' => $statsByFerme,
                    'byType' => $statsByType,
                ],
            ]);
        }

        return view('pages.admin.gestion-oeufs', compact(
            'stocks',
            'totalStocks',
            'totalQuantite',
            'totalOeufs',
            'statsByFerme',
            'statsByType',
            'fermes'
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

        return redirect()->back()->with('success', 'Stock d\'œufs créé avec succès');
    }

    public function update(Request $request, $id)
    {
        $stock = StockOeuf::find($id);

        if (!$stock) {
            return redirect()->back()->with('error', 'Stock non trouvé');
        }

        $request->validate([
            'code_ferme' => 'required|string|max:20',
            'quantite' => 'required|integer|min:0',
            'date_entree' => 'required|date',
        ]);

        $oldQuantite = $stock->quantite;
        $newQuantite = $request->quantite;

        $stock->update([
            'code_ferme' => $request->code_ferme,
            'quantite' => $newQuantite,
            'date_entree' => $request->date_entree,
        ]);

        if ($oldQuantite != $newQuantite) {
            $difference = $newQuantite - $oldQuantite;
            HistoriqueOeuf::create([
                'stock_oeuf_id' => $stock->id,
                'gerant_id' => auth()->id(),
                'type' => $difference > 0 ? 'entree' : 'sortie',
                'quantite' => abs($difference),
                'date_mouvement' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Stock mis à jour avec succès');
    }

    public function destroy($id)
    {
        $stock = StockOeuf::find($id);

        if (!$stock) {
            return redirect()->back()->with('error', 'Stock non trouvé');
        }

        $stock->delete();

        return redirect()->route('admin.oeufs.index')->with('success', 'Stock supprimé avec succès');
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Aucun ID fourni'], 400);
        }

        StockOeuf::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Stocks supprimés avec succès']);
    }

    public function mouvement(Request $request)
    {
        $request->validate([
            'stock_oeuf_id' => 'required|exists:stock_oeufs,id',
            'type' => 'required|in:entree,sortie,casse,vente',
            'quantite' => 'required|integer|min:1',
            'date_mouvement' => 'required|date',
        ]);

        $stock = StockOeuf::find($request->stock_oeuf_id);

        if (!$stock) {
            return redirect()->back()->with('error', 'Stock non trouvé');
        }

        if ($request->type !== 'entree' && $request->quantite > $stock->quantite) {
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

        return redirect()->back()->with('success', 'Mouvement enregistré avec succès');
    }

    public function historique($id)
    {
        $stock = StockOeuf::with(['historiques.gerant'])->find($id);

        if (!$stock) {
            return redirect()->back()->with('error', 'Stock non trouvé');
        }

        return view('pages.admin.historique-stock-oeuf', compact('stock'));
    }
}
