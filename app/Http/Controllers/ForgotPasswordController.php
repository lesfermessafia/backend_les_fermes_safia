<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetCode;
use App\Models\User;
use App\Mail\ForgotPasswordCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function showEmailForm()
    {
        return view('auth.forgot-password-email');
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->input('email');
        $code = (string) random_int(100000, 999999);

        PasswordResetCode::where('email', $email)->delete();

        PasswordResetCode::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($email)->send(new ForgotPasswordCodeMail($code));

        return redirect()->route('password.code.form', ['email' => $email])
            ->with('status', 'Un code de vérification a été envoyé à votre adresse e-mail.');
    }

    public function showCodeForm(Request $request)
    {
        $email = $request->input('email');
        return view('auth.forgot-password-code', compact('email'));
    }

    public function verifyCode(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|digits:6',
        ]);

        $record = PasswordResetCode::where('email', $data['email'])
            ->where('code', $data['code'])
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return back()->withErrors(['code' => 'Code invalide ou expiré.'])->withInput();
        }

        $record->update(['used' => true]);
        session(['password_reset_email' => $data['email']]);

        return redirect()->route('password.reset.form');
    }

    public function showResetForm()
    {
        if (!session('password_reset_email')) {
            return redirect()->route('password.forgot')->withErrors(['email' => 'Session expirée, veuillez recommencer.']);
        }

        return view('auth.forgot-password-reset');
    }

    public function reset(Request $request)
    {
        $email = session('password_reset_email');

        if (!$email) {
            return redirect()->route('password.forgot')->withErrors(['email' => 'Session expirée, veuillez recommencer.']);
        }

        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $email)->firstOrFail();
        $user->update(['password' => bcrypt($request->password)]);

        session()->forget('password_reset_email');
        PasswordResetCode::where('email', $email)->delete();

        return redirect()->route('login')->with('success', 'Votre mot de passe a été réinitialisé avec succès.');
    }
}
