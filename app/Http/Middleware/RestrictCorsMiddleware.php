<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictCorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $allowedOrigins = [
            'http://localhost:8080',
        ];

        $origin = $request->headers->get('Origin');

        if ($origin && in_array($origin, $allowedOrigins)) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization');
            header('Access-Control-Allow-Credentials: true');
        }

        if ($request->getMethod() === "OPTIONS") {
            return response()->json('OK', 200);
        }

        return $next($request);
    }
}
