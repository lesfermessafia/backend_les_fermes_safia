<?php

namespace App\Http\Controllers;

use App\Models\Aliment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlimentController extends Controller
{
    public function index()
    {
        $aliments = Aliment::all()->map(function ($aliment) {
            $aliment->photo_url = $aliment->photo ? url('img/' . $aliment->photo) : null;
            return $aliment;
        });
        return response()->json($aliments);
    }

    public function show($id)
    {
        $aliment = Aliment::find($id);
        
        if (!$aliment) {
            return response()->json(['error' => 'Aliment not found'], 404);
        }
        
        $aliment->photo_url = $aliment->photo ? url('img/' . $aliment->photo) : null;
        return response()->json($aliment);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('imageAliment', $photoName, 'public');
        }

        $aliment = Aliment::create([
            'nom' => $request->nom,
            'code' => Aliment::generateCode(),
            'photo' => $photoPath,
        ]);

        $aliment->photo_url = $aliment->photo ? url('img/' . $aliment->photo) : null;
        return response()->json([
            'message' => 'Aliment created successfully',
            'aliment' => $aliment
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $aliment = Aliment::find($id);
        
        if (!$aliment) {
            return response()->json(['error' => 'Aliment not found'], 404);
        }

        $request->validate([
            'nom' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [];
        if ($request->has('nom')) {
            $updateData['nom'] = $request->nom;
        }

        if ($request->hasFile('photo')) {
            if ($aliment->photo && Storage::disk('public')->exists($aliment->photo)) {
                Storage::disk('public')->delete($aliment->photo);
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $updateData['photo'] = $photo->storeAs('imageAliment', $photoName, 'public');
        }

        $aliment->update($updateData);

        $aliment->photo_url = $aliment->photo ? url('img/' . $aliment->photo) : null;
        return response()->json([
            'message' => 'Aliment updated successfully',
            'aliment' => $aliment
        ]);
    }

    public function destroy($id)
    {
        $aliment = Aliment::find($id);
        
        if (!$aliment) {
            return response()->json(['error' => 'Aliment not found'], 404);
        }

        $aliment->delete();

        return response()->json([
            'message' => 'Aliment deleted successfully'
        ]);
    }
}
