<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictCorsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigin = 'http://localhost:8000';
        $origin = $request->headers->get('Origin');

        if ($origin === $allowedOrigin) {
            if ($request->getMethod() === 'OPTIONS') {
                return response('OK', 200)
                    ->withHeaders([
                        'Access-Control-Allow-Origin' => $origin,
                        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                        'Access-Control-Allow-Headers' => 'Origin, Content-Type, Authorization',
                        'Access-Control-Allow-Credentials' => 'true',
                    ]);
            }

            $response = $next($request);

            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Authorization');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');

            return $response;
        }

        return response()->json([
            'message' => 'Origem não permitida.'
        ], 403);
    }
}
