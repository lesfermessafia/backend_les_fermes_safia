<?php

namespace App\Http\Controllers;

use App\Models\Poulet;
use Illuminate\Http\Request;

class PouletController extends Controller
{
    public function index()
    {
        $poulets = Poulet::with('arrivages')->get();
        return response()->json($poulets);
    }

    public function show($id)
    {
        $poulet = Poulet::with('arrivages')->find($id);
        
        if (!$poulet) {
            return response()->json(['error' => 'Poulet not found'], 404);
        }
        
        return response()->json($poulet);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'race' => 'required|string',
        ]);

        $poulet = Poulet::create([
            'nom' => $request->nom,
            'race' => $request->race,
            'code' => Poulet::generateCode(),
        ]);

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
        ]);

        $updateData = [];
        if ($request->has('nom')) {
            $updateData['nom'] = $request->nom;
        }
        if ($request->has('race')) {
            $updateData['race'] = $request->race;
        }

        $poulet->update($updateData);

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

        $poulet->delete();

        return response()->json([
            'message' => 'Poulet deleted successfully'
        ]);
    }
}
