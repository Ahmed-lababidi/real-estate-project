<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            abort(403, 'Unauthorized');
        }

        if (! auth()->user()->is_admin) {
            abort(403, 'You do not have access to API documentation.');
        }

        return $next($request);
    }
}
