<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TokenExpirationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && now()->diffInMinutes(Auth::user()->token()->created_at) >= 5) {
            return response()->json(['message' => 'Token expirado. Faça login novamente.'], 401);
        }

        return $next($request);
    }
}
