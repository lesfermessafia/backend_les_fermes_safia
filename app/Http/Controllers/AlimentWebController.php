<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aliment;

class AlimentWebController extends Controller
{
    public function index()
    {
        $aliments = Aliment::all();
        return view('pages.admin.gestion-aliments', compact('aliments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
        ]);

        Aliment::create([
            'nom' => $request->nom,
            'code' => Aliment::generateCode(),
        ]);

        return redirect()->route('admin.aliments.index')->with('success', 'Aliment créé avec succès');
    }

    public function update(Request $request, $id)
    {
        $aliment = Aliment::find($id);

        if (!$aliment) {
            return redirect()->back()->with('error', 'Aliment non trouvé');
        }

        $request->validate([
            'nom' => 'required|string',
        ]);

        $aliment->update([
            'nom' => $request->nom,
        ]);

        return redirect()->route('admin.aliments.index')->with('success', 'Aliment mis à jour avec succès');
    }

    public function destroy($id)
    {
        $aliment = Aliment::find($id);

        if (!$aliment) {
            return redirect()->back()->with('error', 'Aliment non trouvé');
        }

        $aliment->delete();

        return redirect()->route('admin.aliments.index')->with('success', 'Aliment supprimé avec succès');
    }
}
