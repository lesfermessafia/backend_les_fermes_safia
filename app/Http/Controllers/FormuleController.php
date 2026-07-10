<?php

namespace App\Http\Controllers;

use App\Models\Formule;
use App\Models\MatierePremiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
                'photo_url' => $formule->photo ? url('img/' . $formule->photo) : null,
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
            'photo_url' => $formule->photo ? url('img/' . $formule->photo) : null,
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
            $photoPath = $photo->storeAs('imageFormule', $photoName, 'public');
        }

        $formule = Formule::create([
            'nom' => $request->nom,
            'photo' => $photoPath,
            'composant' => $request->composant,
        ]);

        $formule->photo_url = $formule->photo ? url('img/' . $formule->photo) : null;
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
            if ($formule->photo && Storage::disk('public')->exists($formule->photo)) {
                Storage::disk('public')->delete($formule->photo);
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $updateData['photo'] = $photo->storeAs('imageFormule', $photoName, 'public');
        }

        $formule->update($updateData);

        $formule->photo_url = $formule->photo ? url('img/' . $formule->photo) : null;
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

        if ($formule->photo && Storage::disk('public')->exists($formule->photo)) {
            Storage::disk('public')->delete($formule->photo);
        }

        $formule->delete();

        return response()->json([
            'message' => 'Formule deleted successfully'
        ]);
    }
}
