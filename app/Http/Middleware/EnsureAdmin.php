<?php

namespace App\Http\Middleware;

use App\Support\UserAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! UserAccess::isAdmin($request->user())) {
            abort(403, 'You are not authorized to access this module.');
        }

        return $next($request);
    }
}
