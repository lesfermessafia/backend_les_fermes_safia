<?php

namespace App\Http\Controllers;

use App\Models\Poulet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PouletController extends Controller
{
    public function index()
    {
        $poulets = Poulet::with('arrivages')->get()->map(function ($poulet) {
            $poulet->photo_url = $poulet->photo ? url('img/' . $poulet->photo) : null;
            return $poulet;
        });
        return response()->json($poulets);
    }

    public function show($id)
    {
        $poulet = Poulet::with('arrivages')->find($id);
        
        if (!$poulet) {
            return response()->json(['error' => 'Poulet not found'], 404);
        }
        
        $poulet->photo_url = $poulet->photo ? url('img/' . $poulet->photo) : null;
        return response()->json($poulet);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'race' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('imagePoulet', $photoName, 'public');
        }

        $poulet = Poulet::create([
            'nom' => $request->nom,
            'race' => $request->race,
            'code' => Poulet::generateCode(),
            'photo' => $photoPath,
        ]);

        $poulet->photo_url = $poulet->photo ? url('img/' . $poulet->photo) : null;
        return response()->json([
            'message' => 'Poulet created successfully',
            'poulet' => $poulet
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $poulet = Poulet::find($id);
        
        if (!$poulet) {
            return response()->json(['error' => 'Poulet not found'], 404);
        }

        $request->validate([
            'nom' => 'nullable|string',
            'race' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [];
        if ($request->has('nom')) {
            $updateData['nom'] = $request->nom;
        }
        if ($request->has('race')) {
            $updateData['race'] = $request->race;
        }

        if ($request->hasFile('photo')) {
            if ($poulet->photo && Storage::disk('public')->exists($poulet->photo)) {
                Storage::disk('public')->delete($poulet->photo);
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $updateData['photo'] = $photo->storeAs('imagePoulet', $photoName, 'public');
        }

        $poulet->update($updateData);

        $poulet->photo_url = $poulet->photo ? url('img/' . $poulet->photo) : null;
        return response()->json([
            'message' => 'Poulet updated successfully',
            'poulet' => $poulet
        ]);
    }

    public function destroy($id)
    {
        $poulet = Poulet::find($id);
        
        if (!$poulet) {
            return response()->json(['error' => 'Poulet not found'], 404);
        }

        if ($poulet->photo && Storage::disk('public')->exists($poulet->photo)) {
            Storage::disk('public')->delete($poulet->photo);
        }

        $poulet->delete();

        return response()->json([
            'message' => 'Poulet deleted successfully'
        ]);
    }
}
