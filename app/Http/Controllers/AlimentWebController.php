<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        $totalAliments = $query->count();
        $aliments = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'aliments' => $aliments->items(),
                'pagination' => $aliments->links()->render(),
                'total' => $totalAliments,
            ]);
        }

        return view('pages.admin.gestion-aliments', compact('aliments', 'totalAliments'));
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
            if ($aliment->photo && Storage::disk('public')->exists($aliment->photo)) {
                Storage::disk('public')->delete($aliment->photo);
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $updateData['photo'] = $photo->storeAs('imageAliment', $photoName, 'public');
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
