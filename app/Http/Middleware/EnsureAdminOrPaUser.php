<?php

namespace App\Http\Middleware;

use App\Support\UserAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOrPaUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! UserAccess::isAdmin($user) && ! UserAccess::isPaUser($user)) {
            abort(403, 'You are not authorized to access this module.');
        }

        return $next($request);
    }
}

