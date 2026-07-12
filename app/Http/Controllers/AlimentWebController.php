<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Aliment;
use App\Models\StockAliment;
use App\Models\Formule;

class AlimentWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Aliment::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $totalAliments = $query->count();
        $aliments = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $stockAliments = StockAliment::with(['aliment', 'formule'])->orderBy('id', 'desc')->get();
        $stocksTermines = StockAliment::with(['aliment', 'formule'])
            ->where('status', 'production terminer')
            ->orderBy('id', 'desc')
            ->get();
        $formules = Formule::all();

        if ($request->ajax()) {
            return response()->json([
                'aliments' => $aliments->items(),
                'pagination' => $aliments->links()->render(),
                'total' => $totalAliments,
            ]);
        }

        return view('pages.admin.gestion-aliments', compact('aliments', 'totalAliments', 'stockAliments', 'stocksTermines', 'formules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'unite' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('imageAliment', $photoName, 'public');
        }

        Aliment::create([
            'nom' => $request->nom,
            'code' => Aliment::generateCode(),
            'unite' => $request->unite,
            'photo' => $photoPath,
        ]);

        return redirect()->back()->with('success', 'Aliment créé avec succès');
    }

    public function update(Request $request, $id)
    {
        $aliment = Aliment::find($id);

        if (!$aliment) {
            return redirect()->back()->with('error', 'Aliment non trouvé');
        }

        $request->validate([
            'nom' => 'required|string',
            'unite' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'nom' => $request->nom,
            'unite' => $request->unite,
        ];

        if ($request->hasFile('photo')) {
            if ($aliment->photo && Storage::disk('public')->exists($aliment->photo)) {
                Storage::disk('public')->delete($aliment->photo);
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $updateData['photo'] = $photo->storeAs('imageAliment', $photoName, 'public');
        }

        $aliment->update($updateData);

        return redirect()->back()->with('success', 'Aliment mis à jour avec succès');
    }

    public function destroy($id)
    {
        $aliment = Aliment::find($id);

        if (!$aliment) {
            return redirect()->back()->with('error', 'Aliment non trouvé');
        }

        $aliment->delete();

        return redirect()->route('admin.aliments.index')->with('success', 'Aliment supprimé avec succès');
    }

    public function storeStock(Request $request)
    {
        $request->validate([
            'aliment_id' => 'required|exists:aliments,id',
            'formule_id' => 'required|exists:formules,id',
            'quantite_fabriquer' => 'required|numeric|min:0.01',
        ]);

        // Générer un code de stock unique
        do {
            $codeStock = 'st-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (StockAliment::where('code_stock', $codeStock)->exists());

        StockAliment::create([
            'aliment_id' => $request->aliment_id,
            'code_stock' => $codeStock,
            'formule_id' => $request->formule_id,
            'quantite_fabriquer' => $request->quantite_fabriquer,
            'quantite_utiliser' => 0,
            'status' => 'en attente',
        ]);

        return redirect()->back()->with('success', 'Stock d\'aliment créé avec succès');
    }

    public function updateStock(Request $request, $id)
    {
        $stock = StockAliment::find($id);

        if (!$stock) {
            return redirect()->back()->with('error', 'Stock non trouvé');
        }

        $request->validate([
            'aliment_id' => 'required|exists:aliments,id',
            'formule_id' => 'required|exists:formules,id',
            'quantite_fabriquer' => 'required|numeric|min:0.01',
        ]);

        $stock->update([
            'aliment_id' => $request->aliment_id,
            'formule_id' => $request->formule_id,
            'quantite_fabriquer' => $request->quantite_fabriquer,
        ]);

        return redirect()->back()->with('success', 'Stock d\'aliment mis à jour avec succès');
    }

    public function destroyStock($id)
    {
        $stock = StockAliment::find($id);

        if (!$stock) {
            return redirect()->back()->with('error', 'Stock non trouvé');
        }

        // Vérifier les restrictions de suppression
        $historique = \App\Models\HistoriqueAliment::where('stock_aliment_id', $id)->get();
        
        // Vérifier s'il y a des sorties
        $hasSorties = $historique->where('type', 'sortie')->count() > 0;
        if ($hasSorties) {
            return redirect()->back()->with('error', 'Ce stock ne peut pas être supprimé car il contient des mouvements de sortie.');
        }
        
        // Vérifier le nombre d'entrées (max 2: création + 1 entrée supplémentaire)
        $entreesCount = $historique->where('type', 'entree')->count();
        if ($entreesCount > 2) {
            return redirect()->back()->with('error', 'Ce stock ne peut pas être supprimé car il contient plus de 2 mouvements d\'entrée.');
        }

        $stock->delete();

        return redirect()->back()->with('success', 'Stock supprimé avec succès');
    }

    public function mouvementStock(Request $request)
    {
        $request->validate([
            'stock_aliment_id' => 'required|exists:stock_aliments,id',
            'type' => 'required|in:entree,sortie',
            'quantite' => 'required|numeric|min:0.01',
            'date_mouvement' => 'required|date',
        ]);

        $stock = StockAliment::find($request->stock_aliment_id);
        
        if (!$stock) {
            return redirect()->back()->with('error', 'Stock non trouvé');
        }

        // Vérifier la disponibilité pour les sorties
        if ($request->type === 'sortie') {
            $disponible = $stock->quantite_fabriquer - $stock->quantite_utiliser;
            if ($request->quantite > $disponible) {
                return redirect()->back()->with('error', 'Quantité insuffisante pour cette sortie. Disponible: ' . $disponible);
            }
        }

        // Créer l'historique du mouvement
        \App\Models\HistoriqueAliment::create([
            'stock_aliment_id' => $request->stock_aliment_id,
            'gerant_id' => auth()->id(),
            'type' => $request->type,
            'quantite' => $request->quantite,
            'date_mouvement' => $request->date_mouvement,
        ]);

        // Mettre à jour la quantité utilisée ou fabriquée
        if ($request->type === 'entree') {
            $stock->quantite_fabriquer += $request->quantite;
        } else {
            $stock->quantite_utiliser += $request->quantite;
        }

        $stock->save();

        return redirect()->back()->with('success', 'Mouvement enregistré avec succès');
    }

    public function stockDetails($id)
    {
        try {
            $stock = StockAliment::with(['aliment', 'formule', 'historiques' => function($query) {
                $query->orderBy('date_mouvement', 'desc')->orderBy('created_at', 'desc');
            }])->find($id);

            if (!$stock) {
                return response()->json(['error' => 'Stock non trouvé'], 404);
            }

            // Enrichir les composants avec les informations de la matière première
            $composants = [];
            if ($stock->formule && $stock->formule->composant) {
                $composantsAvecInfos = collect($stock->formule->composant)->map(function ($composant) {
                    $matiere = \App\Models\MatierePremiere::find($composant['matiere_id']);
                    return [
                        'matiere_id' => $composant['matiere_id'],
                        'matiere_nom' => $matiere ? $matiere->nom : 'Inconnue',
                        'matiere_unite' => $matiere ? $matiere->unite : '',
                        'quantite' => $composant['quantite'],
                    ];
                });

                foreach ($composantsAvecInfos as $composant) {
                    $quantiteCalculee = ($composant['quantite'] / 100) * $stock->quantite_fabriquer;
                    $composants[] = [
                        'nom' => $composant['matiere_nom'],
                        'pourcentage' => $composant['quantite'],
                        'quantite_calculee' => round($quantiteCalculee, 2),
                    ];
                }
            }

            return response()->json([
                'stock' => $stock,
                'composants' => $composants,
                'historiques' => $stock->historiques,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
