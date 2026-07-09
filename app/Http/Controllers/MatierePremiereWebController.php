<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatierePremiere;

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

        $matieres = $query->orderBy('id', 'desc')->get();

        if ($request->ajax()) {
            return response()->json(['matieres' => $matieres]);
        }

        return view('pages.admin.gestion-matieres-premieres', compact('matieres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'unite' => 'required|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('imageMatierePremiere'), $imageName);
            $imagePath = 'imageMatierePremiere/' . $imageName;
        }

        $matiere = MatierePremiere::create([
            'nom' => $request->nom,
            'image' => $imagePath,
            'unite' => $request->unite,
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
        ]);

        $updateData = [
            'nom' => $request->nom,
            'unite' => $request->unite,
        ];

        if ($request->hasFile('image')) {
            if ($matiere->image && file_exists(public_path($matiere->image))) {
                unlink(public_path($matiere->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('imageMatierePremiere'), $imageName);
            $updateData['image'] = 'imageMatierePremiere/' . $imageName;
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

        $matiere->delete();

        return redirect()->back()->with('success', 'Matière première supprimée avec succès');
    }
}
