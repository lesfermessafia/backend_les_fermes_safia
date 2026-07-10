<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        $totalPoulets = $query->count();
        $poulets = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $statsByRace = (clone $query)
            ->selectRaw('race, COUNT(*) as total')
            ->groupBy('race')
            ->orderBy('total', 'desc')
            ->pluck('total', 'race')
            ->toArray();

        if ($request->ajax()) {
            return response()->json([
                'poulets' => $poulets->items(),
                'pagination' => $poulets->links()->render(),
                'total' => $totalPoulets,
                'byRace' => $statsByRace,
            ]);
        }

        return view('pages.admin.gestion-poulets', compact('poulets', 'totalPoulets', 'statsByRace'));
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
            if ($poulet->photo && Storage::disk('public')->exists($poulet->photo)) {
                Storage::disk('public')->delete($poulet->photo);
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $updateData['photo'] = $photo->storeAs('imagePoulet', $photoName, 'public');
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

        if ($poulet->photo && Storage::disk('public')->exists($poulet->photo)) {
            Storage::disk('public')->delete($poulet->photo);
        }

        $poulet->delete();

        return redirect()->route('admin.poulets.index')->with('success', 'Poulet supprimé avec succès');
    }
}
