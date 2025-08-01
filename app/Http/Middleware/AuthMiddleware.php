<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            session(['url.intended' => $request->fullUrl()]);
            return redirect()->route('auth.view.login')
                ->withErrors(['error' => 'You need to be authenticated!']);
        }
        return $next($request);
    }
}
