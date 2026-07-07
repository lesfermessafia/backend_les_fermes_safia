<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Site;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::with('gerantUser')->get();
        return response()->json($sites);
    }

    public function show($id)
    {
        $site = Site::with('gerantUser')->find($id);
        
        if (!$site) {
            return response()->json(['error' => 'Site not found'], 404);
        }
        
        return response()->json($site);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'adresse' => 'required|string',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'longueur' => 'required|numeric',
            'largeur' => 'required|numeric',
            'gerant' => 'required|exists:users,id',
        ]);

        $site = Site::create([
            'nom' => $request->nom,
            'adresse' => $request->adresse,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'longueur' => $request->longueur,
            'largeur' => $request->largeur,
            'gerant' => $request->gerant,
        ]);

        return response()->json([
            'message' => 'Site created successfully',
            'site' => $site->load('gerantUser')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $site = Site::find($id);
        
        if (!$site) {
            return response()->json(['error' => 'Site not found'], 404);
        }

        $request->validate([
            'nom' => 'nullable|string',
            'adresse' => 'nullable|string',
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
        if ($request->has('adresse')) {
            $updateData['adresse'] = $request->adresse;
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

        $site->update($updateData);

        return response()->json([
            'message' => 'Site updated successfully',
            'site' => $site->load('gerantUser')
        ]);
    }

    public function destroy($id)
    {
        $site = Site::find($id);
        
        if (!$site) {
            return response()->json(['error' => 'Site not found'], 404);
        }

        $site->delete();

        return response()->json(['message' => 'Site deleted successfully']);
    }
}
