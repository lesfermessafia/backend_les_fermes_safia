<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poulet;

class PouletWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Poulet::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('race', 'like', "%{$search}%");
            });
        }

        $poulets = $query->orderBy('id', 'desc')->get();

        if ($request->ajax()) {
            return response()->json(['poulets' => $poulets]);
        }

        return view('pages.admin.gestion-poulets', compact('poulets'));
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
            $photo->move(public_path('imagePoulet'), $photoName);
            $photoPath = 'imagePoulet/' . $photoName;
        }

        Poulet::create([
            'nom' => $request->nom,
            'race' => $request->race,
            'code' => Poulet::generateCode(),
            'photo' => $photoPath,
        ]);

        return redirect()->back()->with('success', 'Poulet créé avec succès');
    }

    public function update(Request $request, $id)
    {
        $poulet = Poulet::find($id);

        if (!$poulet) {
            return redirect()->back()->with('error', 'Poulet non trouvé');
        }

        $request->validate([
            'nom' => 'required|string',
            'race' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'nom' => $request->nom,
            'race' => $request->race,
        ];

        if ($request->hasFile('photo')) {
            if ($poulet->photo && file_exists(public_path($poulet->photo))) {
                unlink(public_path($poulet->photo));
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('imagePoulet'), $photoName);
            $updateData['photo'] = 'imagePoulet/' . $photoName;
        }

        $poulet->update($updateData);

        return redirect()->back()->with('success', 'Poulet mis à jour avec succès');
    }

    public function destroy($id)
    {
        $poulet = Poulet::find($id);

        if (!$poulet) {
            return redirect()->back()->with('error', 'Poulet non trouvé');
        }

        if ($poulet->photo && file_exists(public_path($poulet->photo))) {
            unlink(public_path($poulet->photo));
        }

        $poulet->delete();

        return redirect()->route('admin.poulets.index')->with('success', 'Poulet supprimé avec succès');
    }
}
