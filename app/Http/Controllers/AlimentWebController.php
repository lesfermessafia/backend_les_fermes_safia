<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aliment;

class AlimentWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Aliment::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $aliments = $query->orderBy('id', 'desc')->get();

        if ($request->ajax()) {
            return response()->json(['aliments' => $aliments]);
        }

        return view('pages.admin.gestion-aliments', compact('aliments'));
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
            $photo->move(public_path('imageAliment'), $photoName);
            $photoPath = 'imageAliment/' . $photoName;
        }

        Aliment::create([
            'nom' => $request->nom,
            'code' => Aliment::generateCode(),
            'photo' => $photoPath,
        ]);

        return redirect()->back()->with('success', 'Aliment créé avec succès');
    }

    public function update(Request $request, $id)
    {
        $aliment = Aliment::find($id);

        if (!$aliment) {
            return redirect()->back()->with('error', 'Aliment non trouvé');
        }

        $request->validate([
            'nom' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'nom' => $request->nom,
        ];

        if ($request->hasFile('photo')) {
            if ($aliment->photo && file_exists(public_path($aliment->photo))) {
                unlink(public_path($aliment->photo));
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('imageAliment'), $photoName);
            $updateData['photo'] = 'imageAliment/' . $photoName;
        }

        $aliment->update($updateData);

        return redirect()->back()->with('success', 'Aliment mis à jour avec succès');
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
