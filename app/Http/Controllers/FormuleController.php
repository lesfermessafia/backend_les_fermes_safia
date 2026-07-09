<?php

namespace App\Http\Controllers;

use App\Models\Formule;
use App\Models\MatierePremiere;
use Illuminate\Http\Request;

class FormuleController extends Controller
{
    public function index()
    {
        $formules = Formule::all();
        
        // Enrichir les formules avec les infos des matières premières
        $formulesAvecInfos = $formules->map(function($formule) {
            $composantsAvecInfos = collect($formule->composant)->map(function($composant) {
                $matiere = MatierePremiere::find($composant['matiere_id']);
                return [
                    'matiere_premiere' => $matiere ? [
                        'id' => $matiere->id,
                        'nom' => $matiere->nom,
                        'code' => $matiere->code,
                        'unite' => $matiere->unite,
                    ] : null,
                    'quantite' => $composant['quantite']
                ];
            });
            
            return [
                'id' => $formule->id,
                'nom' => $formule->nom,
                'photo' => $formule->photo,
                'composant' => $composantsAvecInfos,
                'created_at' => $formule->created_at,
                'updated_at' => $formule->updated_at,
            ];
        });
        
        return response()->json($formulesAvecInfos);
    }

    public function show($id)
    {
        $formule = Formule::find($id);
        
        if (!$formule) {
            return response()->json(['error' => 'Formule not found'], 404);
        }
        
        // Enrichir la formule avec les infos des matières premières
        $composantsAvecInfos = collect($formule->composant)->map(function($composant) {
            $matiere = MatierePremiere::find($composant['matiere_id']);
            return [
                'matiere_premiere' => $matiere ? [
                    'id' => $matiere->id,
                    'nom' => $matiere->nom,
                    'code' => $matiere->code,
                    'unite' => $matiere->unite,
                ] : null,
                'quantite' => $composant['quantite']
            ];
        });
        
        $formuleAvecInfos = [
            'id' => $formule->id,
            'nom' => $formule->nom,
            'photo' => $formule->photo,
            'composant' => $composantsAvecInfos,
            'created_at' => $formule->created_at,
            'updated_at' => $formule->updated_at,
        ];
        
        return response()->json($formuleAvecInfos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'composant' => 'required|array',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('imageFormule'), $photoName);
            $photoPath = 'imageFormule/' . $photoName;
        }

        $formule = Formule::create([
            'nom' => $request->nom,
            'photo' => $photoPath,
            'composant' => $request->composant,
        ]);

        return response()->json([
            'message' => 'Formule created successfully',
            'formule' => $formule
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $formule = Formule::find($id);
        
        if (!$formule) {
            return response()->json(['error' => 'Formule not found'], 404);
        }

        $request->validate([
            'nom' => 'nullable|string',
            'composant' => 'nullable|array',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [];
        if ($request->has('nom')) {
            $updateData['nom'] = $request->nom;
        }
        if ($request->has('composant')) {
            $updateData['composant'] = $request->composant;
        }

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

        return response()->json([
            'message' => 'Formule updated successfully',
            'formule' => $formule
        ]);
    }

    public function destroy($id)
    {
        $formule = Formule::find($id);
        
        if (!$formule) {
            return response()->json(['error' => 'Formule not found'], 404);
        }

        $formule->delete();

        return response()->json([
            'message' => 'Formule deleted successfully'
        ]);
    }
}
