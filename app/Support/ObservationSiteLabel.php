<?php

namespace App\Support;

use App\Models\SiteName;

/**
 * Human-readable site column for observation rollups (matches endemic / migratory reports).
 */
final class ObservationSiteLabel
{
    public static function label(?SiteName $selectedSite, string $stationCode, string $tableName): string
    {
        if ($selectedSite !== null) {
            return $selectedSite->name;
        }

        if ($stationCode !== '') {
            $site = SiteName::findByStationCode($stationCode);
            if ($site !== null) {
                return $site->name;
            }

            return $stationCode;
        }

        return match ($tableName) {
            'mariano_tbl' => 'MPL — San Mariano',
            'madupapa_tbl' => 'MPL — Madupapa',
            default => '—',
        };
    }
}
