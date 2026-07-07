<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ferme;

class FermeController extends Controller
{
    public function index()
    {
        $fermes = Ferme::with(['site', 'gerantUser'])->get();
        return response()->json($fermes);
    }

    public function show($id)
    {
        $ferme = Ferme::with(['site', 'gerantUser'])->find($id);
        
        if (!$ferme) {
            return response()->json(['error' => 'Ferme not found'], 404);
        }
        
        return response()->json($ferme);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'idsite' => 'required|exists:sites,id',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'longueur' => 'required|numeric',
            'largeur' => 'required|numeric',
            'gerant' => 'required|exists:users,id',
        ]);

        $ferme = Ferme::create([
            'nom' => $request->nom,
            'idsite' => $request->idsite,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'longueur' => $request->longueur,
            'largeur' => $request->largeur,
            'gerant' => $request->gerant,
        ]);

        return response()->json([
            'message' => 'Ferme created successfully',
            'ferme' => $ferme->load(['site', 'gerantUser'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $ferme = Ferme::find($id);
        
        if (!$ferme) {
            return response()->json(['error' => 'Ferme not found'], 404);
        }

        $request->validate([
            'nom' => 'nullable|string',
            'idsite' => 'nullable|exists:sites,id',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'longueur' => 'nullable|numeric',
            'largeur' => 'nullable|numeric',
            'gerant' => 'nullable|exists:users,id',
        ]);

        $updateData = [];
        if ($request->has('nom')) {
            $updateData['nom'] = $request->nom;
        }
        if ($request->has('idsite')) {
            $updateData['idsite'] = $request->idsite;
        }
        if ($request->has('longitude')) {
            $updateData['longitude'] = $request->longitude;
        }
        if ($request->has('latitude')) {
            $updateData['latitude'] = $request->latitude;
        }
        if ($request->has('longueur')) {
            $updateData['longueur'] = $request->longueur;
        }
        if ($request->has('largeur')) {
            $updateData['largeur'] = $request->largeur;
        }
        if ($request->has('gerant')) {
            $updateData['gerant'] = $request->gerant;
        }

        $ferme->update($updateData);

        return response()->json([
            'message' => 'Ferme updated successfully',
            'ferme' => $ferme->load(['site', 'gerantUser'])
        ]);
    }

    public function destroy($id)
    {
        $ferme = Ferme::find($id);
        
        if (!$ferme) {
            return response()->json(['error' => 'Ferme not found'], 404);
        }

        $ferme->delete();

        return response()->json(['message' => 'Ferme deleted successfully']);
    }
}
