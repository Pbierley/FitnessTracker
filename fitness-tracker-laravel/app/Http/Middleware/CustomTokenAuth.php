<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuthToken;
use Symfony\Component\HttpFoundation\Response;

class CustomTokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $authToken = AuthToken::with('user')
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$authToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Set the authenticated user on the request
        $request->setUserResolver(function () use ($authToken) {
            return $authToken->user;
        });

        return $next($request);
    }
}

