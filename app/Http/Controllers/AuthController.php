<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'numero' => 'required|string',
            'role' => 'required|in:admin,superviseur,comptable',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'numero' => $request->numero,
            'role' => $request->role,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user->makeHidden(['password'])
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth('api')->user();

        if ($user->bloquer) {
            auth('api')->logout();
            return response()->json(['error' => 'Account is blocked'], 403);
        }

        return $this->respondWithToken($token);
    }

    public function webLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->bloquer) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Votre compte a été bloqué. Contactez l\'administrateur.',
                ]);
            }

            // Redirection selon le rôle
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'comptable':
                    return redirect()->route('comptable.dashboard');
                case 'superviseur':
                    return redirect()->route('superviseur.dashboard');
                default:
                    return redirect()->route('home');
            }
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ]);
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function webLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()->makeHidden(['password'])
        ]);
    }

    public function blockUser(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->role === 'admin') {
            return response()->json(['error' => 'Cannot block admin user'], 403);
        }

        $user->bloquer = !$user->bloquer;
        $user->save();

        return response()->json([
            'message' => $user->bloquer ? 'User blocked successfully' : 'User unblocked successfully',
            'user' => $user->makeHidden(['password'])
        ]);
    }

    public function getUsersNonAdmin()
    {
        $users = User::where('role', '!=', 'admin')->get()->makeHidden(['password']);
        return response()->json($users);
    }
}
