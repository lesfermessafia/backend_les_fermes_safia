<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdminOrComptable
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        if ($user->role !== 'admin' && $user->role !== 'comptable') {
            return response()->json(['error' => 'Forbidden - Admin or Comptable access only'], 403);
        }

        return $next($request);
    }
}
