<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatierePremiere;

class MatierePremiereController extends Controller
{
    public function index()
    {
        $matieres = MatierePremiere::all();
        return response()->json($matieres);
    }

    public function show($id)
    {
        $matiere = MatierePremiere::find($id);
        
        if (!$matiere) {
            return response()->json(['error' => 'Matiere premiere not found'], 404);
        }
        
        return response()->json($matiere);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'image' => 'nullable|string',
            'unite' => 'required|string',
        ]);

        $matiere = MatierePremiere::create([
            'nom' => $request->nom,
            'image' => $request->image,
            'unite' => $request->unite,
        ]);

        return response()->json([
            'message' => 'Matiere premiere created successfully',
            'matiere' => $matiere
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $matiere = MatierePremiere::find($id);
        
        if (!$matiere) {
            return response()->json(['error' => 'Matiere premiere not found'], 404);
        }

        $request->validate([
            'nom' => 'nullable|string',
            'image' => 'nullable|string',
            'unite' => 'nullable|string',
        ]);

        $updateData = [];
        if ($request->has('nom')) {
            $updateData['nom'] = $request->nom;
        }
        if ($request->has('image')) {
            $updateData['image'] = $request->image;
        }
        if ($request->has('unite')) {
            $updateData['unite'] = $request->unite;
        }

        $matiere->update($updateData);

        return response()->json([
            'message' => 'Matiere premiere updated successfully',
            'matiere' => $matiere
        ]);
    }

    public function destroy($id)
    {
        $matiere = MatierePremiere::find($id);
        
        if (!$matiere) {
            return response()->json(['error' => 'Matiere premiere not found'], 404);
        }

        $matiere->delete();

        return response()->json(['message' => 'Matiere premiere deleted successfully']);
    }
}
