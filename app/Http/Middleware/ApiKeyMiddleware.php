<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-API-KEY');

        if (empty($key) || $key !== config('app.api_key')) {
            return response()->json([
                'success' => false,
                'message' => 'API key inválida o ausente.',
            ], 401);
        }

        return $next($request);
    }
}
