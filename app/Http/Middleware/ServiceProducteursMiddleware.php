<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceProducteursMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user || !$user->isServiceProducteurs()) {
            abort(403, 'Access denied. Service Producteurs privileges required.');
        }

        return $next($request);
    }
}
