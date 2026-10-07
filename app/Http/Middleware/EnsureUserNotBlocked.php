<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserNotBlocked
{
    /**
     * Handle an incoming request.
     * Prevents blocked users from proceeding and logs them out if authenticated on web.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isBlocked()) {
            if ($request->expectsJson()) {
                // Revoke current token for API request
                if ($user->currentAccessToken()) {
                    $user->currentAccessToken()->delete();
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Your account is blocked.',
                ], 403);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account is blocked.',
            ]);
        }

        return $next($request);
    }
}
