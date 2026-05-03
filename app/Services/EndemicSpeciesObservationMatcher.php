<?php

namespace App\Services;

use App\Models\Species;
use Illuminate\Support\Collection;

/**
 * Links observation rows (common/scientific name) to endemic species catalog rows — one implementation for reports.
 */
final class EndemicSpeciesObservationMatcher
{
    /**
     * @return array{byScientific: array<string, Species>, byCommon: array<string, Species>}
     */
    public function buildLookupMaps(Collection $endemicSpecies): array
    {
        $byScientific = [];
        $byCommon = [];
        foreach ($endemicSpecies as $species) {
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
