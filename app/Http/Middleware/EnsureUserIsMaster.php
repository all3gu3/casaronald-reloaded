<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsMaster
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->esMaster()) {
            abort(403, 'Solo los administradores pueden administrar usuarios.');
        }

        return $next($request);
    }
}
