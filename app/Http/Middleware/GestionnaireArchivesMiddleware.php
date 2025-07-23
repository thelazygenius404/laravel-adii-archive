<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GestionnaireArchivesMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user || !$user->isGestionnaireArchives()) {
            abort(403, 'Access denied. Archive manager privileges required.');
        }

        return $next($request);
    }
}
