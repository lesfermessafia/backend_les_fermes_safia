<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

        $users = $query->orderBy('id', 'desc')->get();

        return view('pages.admin.gestion-utilisateur', compact('users'));
    }

    public function show($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

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
            $photo->move(public_path('photosProfil'), $photoName);
            $photoPath = 'photosProfil/' . $photoName;
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
            if ($user->photo_profil && file_exists(public_path($user->photo_profil))) {
                unlink(public_path($user->photo_profil));
            }

            $photo = $request->file('photo_profil');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->move(public_path('photosProfil'), $photoName);
            $updateData['photo_profil'] = 'photosProfil/' . $photoName;
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

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }
}
