<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formule;
use App\Models\MatierePremiere;

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

        if ($request->ajax()) {
            return response()->json(['formules' => $formules]);
        }

        return view('pages.admin.gestion-formules', compact('formules', 'matieresPremieres'));
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
            $photo->move(public_path('imageFormule'), $photoName);
            $photoPath = 'imageFormule/' . $photoName;
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
            if ($formule->photo && file_exists(public_path($formule->photo))) {
                unlink(public_path($formule->photo));
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('imageFormule'), $photoName);
            $updateData['photo'] = 'imageFormule/' . $photoName;
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

        if ($formule->photo && file_exists(public_path($formule->photo))) {
            unlink(public_path($formule->photo));
        }

        $formule->delete();

        return redirect()->route('admin.formules.index')->with('success', 'Formule supprimée avec succès');
    }
}
