<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureModuleAccess
{
    public function handle(Request $request, Closure $next, string $module)
    {
        $user = $request->user();
        if (! $user || ! $user->canAccess($module)) {
            abort(403, 'Acesso negado a este módulo.');
        }
        return $next($request);
    }
}
