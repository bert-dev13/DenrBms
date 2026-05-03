<?php

namespace App\Services;

use App\Models\Species;
use Illuminate\Support\Collection;

/**
 * Links observation rows to {@see Species} with {@see Species::is_migratory()}.
 */
final class MigratorySpeciesObservationMatcher
{
    /**
     * @return array{byScientific: array<string, Species>, byCommon: array<string, Species>}
     */
    public function buildLookupMaps(Collection $migratorySpecies): array
    {
        $byScientific = [];
        $byCommon = [];
        foreach ($migratorySpecies as $species) {
            $sci = mb_strtolower(trim((string) $species->scientific_name));
            if ($sci !== '') {
                $byScientific[$sci] = $species;
            }
            $common = mb_strtolower(trim((string) $species->name));
            if ($common !== '') {
                $byCommon[$common] = $species;
            }
        }

        return ['byScientific' => $byScientific, 'byCommon' => $byCommon];
    }

    /**
     * @param  array<string, Species>  $byScientific
     * @param  array<string, Species>  $byCommon
     */
    public function resolveSpecies(object $row, array $byScientific, array $byCommon): ?Species
    {
        $scientific = mb_strtolower(trim((string) ($row->scientific_name ?? '')));
        if ($scientific !== '' && isset($byScientific[$scientific])) {
            return $byScientific[$scientific];
        }

        $common = mb_strtolower(trim((string) ($row->common_name ?? '')));
        if ($common !== '' && isset($byCommon[$common])) {
            return $byCommon[$common];
        }

        return null;
    }
}
