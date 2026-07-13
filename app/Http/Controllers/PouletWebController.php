<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Poulet;
use App\Models\StockPoulet;
use App\Models\Ferme;

class PouletWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Poulet::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('race', 'like', "%{$search}%");
            });
        }

        $totalPoulets = $query->count();
        $poulets = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $statsByRace = (clone $query)
            ->selectRaw('race, COUNT(*) as total')
            ->groupBy('race')
            ->orderBy('total', 'desc')
            ->pluck('total', 'race')
            ->toArray();

        // Données des stocks de poulets
        $stocksQuery = StockPoulet::with(['ferme', 'poulet']);

        if ($search = $request->input('search')) {
            $stocksQuery->where(function ($q) use ($search) {
                $q->where('code_stock', 'like', "%{$search}%")
                  ->orWhereHas('poulet', function ($q) use ($search) {
                      $q->where('nom', 'like', "%{$search}%");
                  });
            });
        }

        $totalStocks = $stocksQuery->count();
        $stocks = $stocksQuery->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $statsByPouletStocks = (clone StockPoulet::query())
            ->with('poulet')
            ->selectRaw('poulet_id, SUM(quantite) as total')
            ->groupBy('poulet_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->poulet ? $item->poulet->nom : 'Non assigné' => $item->total];
            })
            ->toArray();

        $statsByStatutStocks = (clone StockPoulet::query())
            ->selectRaw('statut, SUM(quantite) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut')
            ->toArray();

        $totalQuantiteStocks = StockPoulet::sum('quantite');
        $totalMorts = StockPoulet::where('statut', 'mort')->sum('quantite');
        $totalVendus = StockPoulet::where('statut', 'vendu')->sum('quantite');
        $totalEnStock = StockPoulet::where('statut', 'en_stock')->sum('quantite');
        $totalEnProduction = StockPoulet::where('statut', 'en_production')->sum('quantite');

        $fermes = Ferme::all();
        $pouletsStocks = Poulet::all();

        if ($request->ajax()) {
            return response()->json([
                'poulets' => $poulets->items(),
                'pagination' => $poulets->links()->render(),
                'total' => $totalPoulets,
                'byRace' => $statsByRace,
            ]);
        }

        return view('pages.admin.gestion-poulets', compact(
            'poulets',
            'totalPoulets',
            'statsByRace',
            'stocks',
            'totalStocks',
            'totalQuantiteStocks',
            'statsByPouletStocks',
            'statsByStatutStocks',
            'totalMorts',
            'totalVendus',
            'totalEnStock',
            'totalEnProduction',
            'fermes',
            'pouletsStocks'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'race' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('imagePoulet', $photoName, 'public');
        }

        Poulet::create([
            'nom' => $request->nom,
            'race' => $request->race,
            'code' => Poulet::generateCode(),
            'photo' => $photoPath,
        ]);

        return redirect()->back()->with('success', 'Poulet créé avec succès');
    }

    public function update(Request $request, $id)
    {
        $poulet = Poulet::find($id);

        if (!$poulet) {
            return redirect()->back()->with('error', 'Poulet non trouvé');
        }

        $request->validate([
            'nom' => 'required|string',
            'race' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'nom' => $request->nom,
            'race' => $request->race,
        ];

        if ($request->hasFile('photo')) {
            if ($poulet->photo && Storage::disk('public')->exists($poulet->photo)) {
                Storage::disk('public')->delete($poulet->photo);
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $updateData['photo'] = $photo->storeAs('imagePoulet', $photoName, 'public');
        }

        $poulet->update($updateData);

        return redirect()->back()->with('success', 'Poulet mis à jour avec succès');
    }

    public function destroy($id)
    {
        $poulet = Poulet::find($id);

        if (!$poulet) {
            return redirect()->back()->with('error', 'Poulet non trouvé');
        }

        if ($poulet->photo && Storage::disk('public')->exists($poulet->photo)) {
            Storage::disk('public')->delete($poulet->photo);
        }

        $poulet->delete();

        return redirect()->route('admin.poulets.index')->with('success', 'Poulet supprimé avec succès');
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Aucun ID fourni'], 400);
        }

        $poulets = Poulet::whereIn('id', $ids)->get();

        foreach ($poulets as $poulet) {
            if ($poulet->photo && Storage::disk('public')->exists($poulet->photo)) {
                Storage::disk('public')->delete($poulet->photo);
            }
        }

        Poulet::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Poulets supprimés avec succès']);
    }
}
