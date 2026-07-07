<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Magasin;

class MagasinController extends Controller
{
    public function index()
    {
        $magasins = Magasin::with(['site', 'gerantUser'])->get();
        return response()->json($magasins);
    }

    public function show($id)
    {
        $magasin = Magasin::with(['site', 'gerantUser'])->find($id);
        
        if (!$magasin) {
            return response()->json(['error' => 'Magasin not found'], 404);
        }
        
        return response()->json($magasin);
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

        $magasin = Magasin::create([
            'nom' => $request->nom,
            'idsite' => $request->idsite,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'longueur' => $request->longueur,
            'largeur' => $request->largeur,
            'gerant' => $request->gerant,
        ]);

        return response()->json([
            'message' => 'Magasin created successfully',
            'magasin' => $magasin->load(['site', 'gerantUser'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $magasin = Magasin::find($id);
        
        if (!$magasin) {
            return response()->json(['error' => 'Magasin not found'], 404);
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

        $magasin->update($updateData);

        return response()->json([
            'message' => 'Magasin updated successfully',
            'magasin' => $magasin->load(['site', 'gerantUser'])
        ]);
    }

    public function destroy($id)
    {
        $magasin = Magasin::find($id);
        
        if (!$magasin) {
            return response()->json(['error' => 'Magasin not found'], 404);
        }

        $magasin->delete();

        return response()->json(['message' => 'Magasin deleted successfully']);
    }
}
