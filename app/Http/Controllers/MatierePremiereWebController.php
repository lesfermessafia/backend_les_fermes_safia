<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\MatierePremiere;
use App\Models\Magasin;
use App\Models\Lot;

class MatierePremiereWebController extends Controller
{
    public function index(Request $request)
    {
        $query = MatierePremiere::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('unite', 'like', "%{$search}%");
            });
        }

        $totalMatieres = $query->count();
        $matieres = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $statsByUnite = (clone $query)
            ->selectRaw('unite, COUNT(*) as total')
            ->groupBy('unite')
            ->orderBy('total', 'desc')
            ->pluck('total', 'unite')
            ->toArray();

        if ($request->ajax()) {
            return response()->json([
                'matieres' => $matieres->items(),
                'pagination' => $matieres->links()->render(),
                'total' => $totalMatieres,
                'byUnite' => $statsByUnite,
            ]);
        }

        $magasins = Magasin::all();
        $lots = Lot::all();
        $allMatieres = MatierePremiere::all();

        return view('pages.admin.gestion-matieres-premieres', compact('matieres', 'totalMatieres', 'statsByUnite', 'magasins', 'lots', 'allMatieres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'unite' => 'required|string',
            'seuil_alerte' => 'nullable|numeric|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('imageMatierePremiere', $imageName, 'public');
        }

        $matiere = MatierePremiere::create([
            'nom' => $request->nom,
            'image' => $imagePath,
            'unite' => $request->unite,
            'seuil_alerte' => $request->filled('seuil_alerte') ? $request->seuil_alerte : 10,
        ]);

        return redirect()->back()->with('success', 'Matière première créée avec succès');
    }

    public function update(Request $request, $id)
    {
        $matiere = MatierePremiere::find($id);

        if (!$matiere) {
            return redirect()->back()->with('error', 'Matière première non trouvée');
        }

        $request->validate([
            'nom' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'unite' => 'required|string',
            'seuil_alerte' => 'nullable|numeric|min:0',
        ]);

        $updateData = [
            'nom' => $request->nom,
            'unite' => $request->unite,
            'seuil_alerte' => $request->filled('seuil_alerte') ? $request->seuil_alerte : 10,
        ];

        if ($request->hasFile('image')) {
            if ($matiere->image && Storage::disk('public')->exists($matiere->image)) {
                Storage::disk('public')->delete($matiere->image);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $updateData['image'] = $image->storeAs('imageMatierePremiere', $imageName, 'public');
        }

        $matiere->update($updateData);

        return redirect()->back()->with('success', 'Matière première mise à jour avec succès');
    }

    public function destroy($id)
    {
        $matiere = MatierePremiere::find($id);
        
        if (!$matiere) {
            return redirect()->back()->with('error', 'Matière première non trouvée');
        }

        if ($matiere->image && Storage::disk('public')->exists($matiere->image)) {
            Storage::disk('public')->delete($matiere->image);
        }

        $matiere->delete();

        return redirect()->back()->with('success', 'Matière première supprimée avec succès');
    }

    public function getAll()
    {
        $matieres = MatierePremiere::select('id', 'nom', 'code', 'unite')->get();
        return response()->json($matieres);
    }
}
