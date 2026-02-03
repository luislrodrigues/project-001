<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermissionOr
{
    /**
     * Handle an incoming request. Allows access if user has ANY of the given permissions.
     */
    public function handle(Request $request, Closure $next, string $permissions): mixed
    {
        if (!Auth::check()) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        $list = array_map('trim', explode(',', $permissions));
        foreach ($list as $permission) {
            if (Auth::user()->hasPermissionTo($permission)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes permiso para acceder a esta página.');
    }
}
