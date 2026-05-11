<?php

namespace App\Http\Middleware;

use App\Models\SiteName;
use App\Support\UserAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceProtectedAreaScope
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $assignedProtectedAreaId = UserAccess::assignedProtectedAreaId($user);

        if ($assignedProtectedAreaId === null) {
            return $next($request);
        }

        $request->merge(['protected_area_id' => $assignedProtectedAreaId]);

        $routeProtectedAreaId = $this->extractRouteProtectedAreaId($request);
        if ($routeProtectedAreaId !== null && $routeProtectedAreaId !== $assignedProtectedAreaId) {
            abort(403, 'You can only access your assigned protected area.');
        }

        $siteId = $this->extractSiteId($request);
        if ($siteId !== null) {
            $siteBelongsToArea = SiteName::query()
                ->whereKey($siteId)
                ->where('protected_area_id', $assignedProtectedAreaId)
                ->exists();

            if (! $siteBelongsToArea) {
                abort(403, 'Selected site does not belong to your assigned protected area.');
            }
        }

        return $next($request);
    }

    private function extractRouteProtectedAreaId(Request $request): ?int
    {
        foreach (['protectedAreaId', 'protected_area_id'] as $key) {
            $value = $request->route($key);
            if (is_numeric($value)) {
                return (int) $value;
            }
        }

        $protectedArea = $request->route('protectedArea');
        if (is_object($protectedArea) && isset($protectedArea->id)) {
            return (int) $protectedArea->id;
        }

        return null;
    }

    private function extractSiteId(Request $request): ?int
    {
        foreach (['site_id', 'site_name_id', 'site_name'] as $key) {
            $value = $request->input($key);
            if (is_numeric($value) && (int) $value > 0) {
                return (int) $value;
            }
        }

        $siteModel = $request->route('siteName');
        if (is_object($siteModel) && isset($siteModel->id)) {
            return (int) $siteModel->id;
        }

        return null;
    }
}
