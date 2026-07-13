<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Formule;
use App\Models\MatierePremiere;
use App\Models\StockAliment;
use App\Models\Aliment;
use Illuminate\Pagination\LengthAwarePaginator;

class FormuleWebController extends Controller
{
    public function index(Request $request)
    {
        $matieresPremieres = MatierePremiere::all();

        $formules = Formule::all()->map(function ($formule) {
            $composantsAvecInfos = collect($formule->composant)->map(function ($composant) {
                $matiere = MatierePremiere::find($composant['matiere_id']);
                return [
                    'matiere_id' => $composant['matiere_id'],
                    'matiere_nom' => $matiere ? $matiere->nom : 'Inconnue',
                    'matiere_unite' => $matiere ? $matiere->unite : '',
                    'quantite' => $composant['quantite'],
                ];
            });

            return [
                'id' => $formule->id,
                'nom' => $formule->nom,
                'photo' => $formule->photo,
                'composant' => $composantsAvecInfos,
            ];
        });

        if ($search = $request->input('search')) {
            $searchLower = mb_strtolower($search);
            $formules = $formules->filter(function ($formule) use ($searchLower) {
                if (str_contains(mb_strtolower($formule['nom']), $searchLower)) {
                    return true;
                }
                return collect($formule['composant'])->contains(function ($c) use ($searchLower) {
                    return str_contains(mb_strtolower($c['matiere_nom']), $searchLower);
                });
            })->values();
        }

        $totalFormules = $formules->count();
        $totalComposants = $formules->sum(fn ($f) => count($f['composant']));
        $avgComposants = $totalFormules > 0 ? round($totalComposants / $totalFormules, 2) : 0;

        $page = $request->input('page', 1);
        $perPage = 10;
        $formules = new LengthAwarePaginator(
            $formules->forPage($page, $perPage)->values(),
            $formules->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Récupérer les statistiques d'utilisation des formules dans les stocks
        $formuleUsageStats = [];
        $allFormules = Formule::all();
        foreach ($allFormules as $formule) {
            $stockCount = StockAliment::where('formule_id', $formule->id)->count();
            $formuleUsageStats[$formule->id] = [
                'count' => $stockCount,
                'stocks' => StockAliment::with(['aliment'])
                    ->where('formule_id', $formule->id)
                    ->get()
                    ->map(function ($stock) {
                        return [
                            'id' => $stock->id,
                            'code_stock' => $stock->code_stock,
                            'aliment_nom' => $stock->aliment ? $stock->aliment->nom : 'Inconnu',
                            'aliment_code' => $stock->aliment ? $stock->aliment->code : '',
                            'quantite_fabriquer' => $stock->quantite_fabriquer,
                            'quantite_utiliser' => $stock->quantite_utiliser,
                            'status' => $stock->status,
                        ];
                    })
            ];
        }

        $stats = [
            'total' => $totalFormules,
            'totalComposants' => $totalComposants,
            'avgComposants' => $avgComposants,
        ];

        if ($request->ajax()) {
            return response()->json([
                'formules' => $formules->items(),
                'pagination' => $formules->links()->render(),
                'stats' => $stats,
            ]);
        }

        return view('pages.admin.gestion-formules', compact('formules', 'matieresPremieres', 'stats', 'formuleUsageStats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'composant' => 'required|array|min:1',
            'composant.*.matiere_id' => 'required|exists:matiere_premieres,id',
            'composant.*.quantite' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('imageFormule', $photoName, 'public');
        }

        Formule::create([
            'nom' => $request->nom,
            'photo' => $photoPath,
            'composant' => $request->composant,
        ]);

        return redirect()->back()->with('success', 'Formule créée avec succès');
    }

    public function update(Request $request, $id)
    {
        $formule = Formule::find($id);

        if (!$formule) {
            return redirect()->back()->with('error', 'Formule non trouvée');
        }

        $request->validate([
            'nom' => 'required|string',
            'composant' => 'required|array|min:1',
            'composant.*.matiere_id' => 'required|exists:matiere_premieres,id',
            'composant.*.quantite' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'nom' => $request->nom,
            'composant' => $request->composant,
        ];

        if ($request->hasFile('photo')) {
            if ($formule->photo && Storage::disk('public')->exists($formule->photo)) {
                Storage::disk('public')->delete($formule->photo);
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $updateData['photo'] = $photo->storeAs('imageFormule', $photoName, 'public');
        }

        $formule->update($updateData);

        return redirect()->back()->with('success', 'Formule mise à jour avec succès');
    }

    public function destroy($id)
    {
        $formule = Formule::find($id);

        if (!$formule) {
            return redirect()->back()->with('error', 'Formule non trouvée');
        }

        if ($formule->photo && Storage::disk('public')->exists($formule->photo)) {
            Storage::disk('public')->delete($formule->photo);
        }

        $formule->delete();

        return redirect()->route('admin.formules.index')->with('success', 'Formule supprimée avec succès');
    }
}
