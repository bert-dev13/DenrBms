<?php

namespace App\Services;

use App\Models\ProtectedArea;
use App\Models\SiteName;
use App\Models\User;
use App\Support\UserAccess;
use Illuminate\Support\Collection;

/**
 * Single source of truth for resolving Site Name dropdown options.
 *
 * Use this from every controller / endpoint that surfaces sites so that the
 * scoping rules (admin sees all, PA user sees only their assigned area) stay
 * consistent and bugs only need to be fixed in one place.
 */
final class SiteResolver
{
    /**
     * Sites for an explicit protected area id, with legacy-name fallbacks for
     * the PA codes that were seeded before sites had a foreign key set.
     *
     * @return Collection<int, SiteName>
     */
    public function sitesForProtectedAreaId(?int $protectedAreaId): Collection
    {
        if ($protectedAreaId === null) {
            return collect();
        }

        $protectedArea = ProtectedArea::find($protectedAreaId);
        if (! $protectedArea) {
            return collect();
        }

        return $this->sitesForProtectedArea($protectedArea);
    }

    /**
     * Sites for a known protected area, with legacy-name fallbacks.
     *
     * @return Collection<int, SiteName>
     */
    public function sitesForProtectedArea(ProtectedArea $protectedArea): Collection
    {
        $siteNames = SiteName::where('protected_area_id', $protectedArea->id)
            ->orderBy('name')
            ->get();

        if ($siteNames->isNotEmpty()) {
            return $siteNames;
        }

        // Fallback only for known legacy codes where seed data may not yet have
        // set protected_area_id. An empty `where` clause would otherwise pull
        // unrelated sites for arbitrary protected areas.
        if ($protectedArea->code === 'PPLS') {
            return SiteName::where('name', 'like', 'PPLS Site%')
                ->orderBy('name')
                ->get();
        }

        if ($protectedArea->code === 'MPL') {
            return SiteName::where(function ($query) {
                $query->where('name', 'like', 'MPL SITE%')
                    ->orWhere('name', 'like', 'MPL Site%');
            })->orderBy('name')->get();
        }

        return collect();
    }

    /**
     * Sites accessible to a user, optionally filtered by the protected area
     * currently selected in the request.
     *
     * - Admin + no protected_area_id selected → empty (dropdown is disabled
     *   until a PA is selected, matching the historical UI behavior).
     * - Admin + protected_area_id selected → sites for that PA.
     * - PA user → always restricted to their assigned PA, regardless of any
     *   protected_area_id value posted from the browser.
     *
     * @return Collection<int, SiteName>
     */
    public function sitesForUserAndProtectedAreaId(?User $user, ?int $requestedProtectedAreaId): Collection
    {
        $assignedProtectedAreaId = UserAccess::assignedProtectedAreaId($user);

        $effectiveProtectedAreaId = $assignedProtectedAreaId
            ?? ($requestedProtectedAreaId ?: null);

        return $this->sitesForProtectedAreaId($effectiveProtectedAreaId);
    }

    /**
     * Count of sites under the user's assigned Protected Area. Returns 0 for
     * non-PA users (admin/super admin/etc.) so the "Total Sites" summary card
     * can be safely rendered with this value only when `isPaScoped` is true.
     */
    public function siteCountForUser(?User $user): int
    {
        $assignedProtectedAreaId = UserAccess::assignedProtectedAreaId($user);
        if ($assignedProtectedAreaId === null) {
            return 0;
        }

        return $this->sitesForProtectedAreaId($assignedProtectedAreaId)->count();
    }
}
