<?php

namespace App\Http\Controllers;

use App\Models\Aliment;
use Illuminate\Http\Request;

class AlimentController extends Controller
{
    public function index()
    {
        $aliments = Aliment::all();
        return response()->json($aliments);
    }

    public function show($id)
    {
        $aliment = Aliment::find($id);
        
        if (!$aliment) {
            return response()->json(['error' => 'Aliment not found'], 404);
        }
        
        return response()->json($aliment);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
        ]);

        $aliment = Aliment::create([
            'nom' => $request->nom,
            'code' => Aliment::generateCode(),
        ]);

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
        ]);

        $updateData = [];
        if ($request->has('nom')) {
            $updateData['nom'] = $request->nom;
        }

        $aliment->update($updateData);

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
