<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $isAdmin = session('role') === 'admin'
            || ($request->user()->role ?? null) === 'admin';

        if (!$isAdmin) {
            return $request->expectsJson()
                ? response()->json(['error' => 'Forbidden'], 403)
                : redirect()->route('admin.login');
        }

        return $next($request);
    }
}