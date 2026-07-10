<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('numero', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->input('role') !== 'all') {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('bloquer', $request->input('status') === 'bloque' ? 1 : 0);
        }

        $users = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $roleStats = User::selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();

        $statusStats = [
            'actif' => User::where('bloquer', 0)->count(),
            'bloque' => User::where('bloquer', 1)->count(),
        ];

        return view('pages.admin.gestion-utilisateur', compact('users', 'roleStats', 'statusStats'));
    }

    public function show($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->photo_profil_url = $user->photo_profil ? url('img/' . $user->photo_profil) : null;
        return response()->json($user->makeHidden(['password']));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:users',
            'numero' => 'required|string',
            'role' => 'required|in:admin,comptable,superviseur',
            'password' => 'required|string|min:6',
            'photo_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo_profil')) {
            $photo = $request->file('photo_profil');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('photosProfil', $photoName, 'public');
        }

        User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'numero' => $request->numero,
            'photo_profil' => $photoPath,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'bloquer' => false,
        ]);

        return redirect()->back()->with('success', 'Utilisateur créé avec succès');
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return back()->with('error', 'Utilisateur non trouvé');
        }

        $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'numero' => 'required|string',
            'role' => 'required|in:admin,comptable,superviseur',
            'photo_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'numero' => $request->numero,
            'role' => $request->role,
        ];

        if ($request->hasFile('photo_profil')) {
            if ($user->photo_profil && Storage::disk('public')->exists($user->photo_profil)) {
                Storage::disk('public')->delete($user->photo_profil);
            }

            $photo = $request->file('photo_profil');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $updateData['photo_profil'] = $photo->storeAs('photosProfil', $photoName, 'public');
        }

        $user->update($updateData);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->back()->with('success', 'Utilisateur mis à jour avec succès');
    }

    public function toggleBlock($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        if ($user->role === 'admin') {
            return response()->json(['error' => 'Impossible de bloquer un administrateur'], 403);
        }

        $user->bloquer = !$user->bloquer;
        $user->save();

        return response()->json(['message' => 'Statut de l\'utilisateur mis à jour avec succès']);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        if ($user->role === 'admin') {
            return response()->json(['error' => 'Impossible de supprimer un administrateur'], 403);
        }

        if ($user->photo_profil && Storage::disk('public')->exists($user->photo_profil)) {
            Storage::disk('public')->delete($user->photo_profil);
        }

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }
}
