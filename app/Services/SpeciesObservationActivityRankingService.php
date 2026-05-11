<?php

namespace App\Services;

use App\Support\ObservationRowValue;
use Illuminate\Support\Collection;

final class SpeciesObservationActivityRankingService
{
    public function __construct(
        private SpeciesCanonicalResolver $speciesCanonicalResolver,
    ) {}

    /**
     * Same grouping and ordering rules as the Species Activity report (rank_order asc/desc on observation frequency, then highest single-record count).
     *
     * @param  'asc'|'desc'  $rankOrder
     * @return Collection<int, object>
     */
    public function rankForSpeciesActivity(Collection $allResults, string $rankOrder): Collection
    {
        $items = $this->aggregateSpeciesGroups($allResults);
        $ascending = $rankOrder === 'asc';
        usort($items, static function (array $a, array $b) use ($ascending): int {
            $freqCmp = ((int) $a['observation_frequency']) <=> ((int) $b['observation_frequency']);
            if ($freqCmp !== 0) {
                return $ascending ? $freqCmp : -$freqCmp;
            }
            $maxCmp = ((int) $a['highest_recorded_count']) <=> ((int) $b['highest_recorded_count']);

            return $ascending ? $maxCmp : -$maxCmp;
        });

        return $this->groupsToRankedCollection($items);
    }

    /**
     * Same per-species aggregates as Species Activity; sorted by total recorded count (Σ) descending, then observation entry count descending.
     *
     * @return Collection<int, object>
     */
    public function rankByRecordedSumThenObservationCount(Collection $allResults): Collection
    {
        $items = $this->aggregateSpeciesGroups($allResults);
        usort($items, static function (array $a, array $b): int {
            $sumCmp = ((int) $b['recorded_count_sum']) <=> ((int) $a['recorded_count_sum']);
            if ($sumCmp !== 0) {
                return $sumCmp;
            }

            return ((int) $b['observation_frequency']) <=> ((int) $a['observation_frequency']);
        });

        return $this->groupsToRankedCollection($items);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function aggregateSpeciesGroups(Collection $allResults): array
    {
        $groups = [];
        foreach ($allResults as $row) {
            $scientific = trim((string) (ObservationRowValue::field($row, 'scientific_name') ?? ''));
            $common = trim((string) (ObservationRowValue::field($row, 'common_name') ?? ''));
            $speciesIdRaw = ObservationRowValue::field($row, 'species_id');
            $resolved = $this->speciesCanonicalResolver->resolve(
                $scientific,
                $common,
                is_numeric($speciesIdRaw) ? (int) $speciesIdRaw : null
            );
            $key = (string) ($resolved['key'] ?? '');
            if ($key === '') {
                continue;
            }

            if (! isset($groups[$key])) {
                $groups[$key] = [
                    'species_id' => $resolved['species_id'],
                    'species_name' => (string) ($resolved['common_name'] ?? ''),
                    'scientific_name' => (string) ($resolved['scientific_name'] ?? ''),
                    'recorded_count_sum' => 0,
                    'highest_recorded_count' => 0,
                    'observation_frequency' => 0,
                    'protected_area_ids' => [],
                ];
            }

            $recordedCount = ObservationRowValue::recordedCount($row);
            $groups[$key]['recorded_count_sum'] += $recordedCount;
            if ($recordedCount > $groups[$key]['highest_recorded_count']) {
                $groups[$key]['highest_recorded_count'] = $recordedCount;
            }
            $groups[$key]['observation_frequency']++;
            if (is_numeric($row->protected_area_id ?? null)) {
                $groups[$key]['protected_area_ids'][(int) $row->protected_area_id] = true;
            }
        }

        return array_values($groups);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return Collection<int, object>
     */
    private function groupsToRankedCollection(array $items): Collection
    {
        return collect($items)->map(static fn (array $g) => (object) [
            'species_id' => $g['species_id'],
            'species_name' => $g['species_name'],
            'scientific_name' => $g['scientific_name'],
            'highest_recorded_count' => (int) $g['highest_recorded_count'],
            'recorded_count_sum' => (int) $g['recorded_count_sum'],
            'observation_frequency' => (int) $g['observation_frequency'],
            'protected_area_count' => count($g['protected_area_ids']),
        ])->values();
    }
}
