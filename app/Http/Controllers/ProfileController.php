<?php

namespace App\Http\Controllers;

use App\Mail\ProfilePasswordCodeMail;
use App\Models\ProfilePasswordCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'numero' => 'required|string|max:255',
        ]);

        $user->update($data);

        return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function sendPasswordCode(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'old_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($data['old_password'], $user->password)) {
            return redirect()->back()
                ->with('open_profile', true)
                ->withErrors(['old_password' => 'L’ancien mot de passe est incorrect.'])
                ->withInput();
        }

        ProfilePasswordCode::where('user_id', $user->id)->delete();

        $code = (string) random_int(100000, 999999);

        ProfilePasswordCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'new_password' => Hash::make($data['password']),
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($user->email)->send(new ProfilePasswordCodeMail($code));

        return redirect()->back()
            ->with('open_profile', true)
            ->with('password_code_sent', true)
            ->with('status', 'Un code de vérification a été envoyé à votre adresse e-mail.');
    }

    public function verifyPasswordCode(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'code' => 'required|digits:6',
        ]);

        $record = ProfilePasswordCode::where('user_id', $user->id)
            ->where('code', $data['code'])
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return redirect()->back()
                ->with('open_profile', true)
                ->with('password_code_sent', true)
                ->withErrors(['code' => 'Code invalide ou expiré.']);
        }

        // Mise à jour directe pour éviter un double hachage du mot de passe
        \App\Models\User::where('id', $user->id)->update(['password' => $record->new_password]);
        $record->update(['used' => true]);

        return redirect()->back()->with('success', 'Votre mot de passe a été mis à jour avec succès.');
    }
}
