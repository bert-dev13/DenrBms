<?php

namespace App\Services;

use App\Data\ObservationFactFilter;
use App\Models\SiteName;
use App\Models\Species;
use App\Support\ObservationRowValue;
use Illuminate\Http\Request;

final class AnalyticsService
{
    public function __construct(
        private SpeciesObservationFactService $observationFactService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function buildAnalyticsDataset(Request $request): array
    {
        $filter = ObservationFactFilter::fromSpeciesObservationStyleRequest($request);
        $facts = $this->observationFactService->getFactsWithSummary($filter, $request);
        $rows = $facts['rows'];

        $summaryStats = $facts['summaryStats'];
        $summaryStats['endemic_observations'] = 0;
        $summaryStats['migratory_observations'] = 0;

        $speciesRegistry = Species::query()
            ->get(['name', 'scientific_name', 'is_endemic', 'is_migratory', 'conservation_status']);
        $speciesLookup = [];
        foreach ($speciesRegistry as $row) {
            $scientific = mb_strtolower(trim((string) $row->scientific_name));
            if ($scientific === '') {
                continue;
            }
            $speciesLookup[$scientific] = [
                'name' => (string) ($row->name ?? ''),
                'is_endemic' => (bool) $row->is_endemic,
                'is_migratory' => (bool) $row->is_migratory,
                'conservation_status' => (string) ($row->conservation_status ?? ''),
            ];
        }

        $timeseries = [];
        $yearlyTimeseries = [];
        $topAreas = [];
        $topSpecies = [];
        $bioGroupBreakdown = [];
        $speciesPeriodMatrix = [];

        $missingScientificNameCount = 0;
        $unknownStationCodes = [];
        $stationMappingCache = [];
        $missingScientificRows = [];

        foreach ($rows as $row) {
            $recordedCount = ObservationRowValue::recordedCount($row);
            $year = (int) (ObservationRowValue::field($row, 'patrol_year') ?? 0);
            $semester = (int) (ObservationRowValue::field($row, 'patrol_semester') ?? 0);
            $periodKey = null;

            if ($year > 0 && in_array($semester, [1, 2], true)) {
                $periodKey = $year.'-S'.$semester;
                if (! isset($timeseries[$periodKey])) {
                    $timeseries[$periodKey] = [
                        'label' => $year.' S'.$semester,
                        'year' => $year,
                        'semester' => $semester,
                        'observation_count' => 0,
                        'recorded_count_sum' => 0,
                        'species_set' => [],
                    ];
                }
                $timeseries[$periodKey]['observation_count']++;
                $timeseries[$periodKey]['recorded_count_sum'] += $recordedCount;
            }

            if ($year > 0) {
                if (! isset($yearlyTimeseries[$year])) {
                    $yearlyTimeseries[$year] = [
                        'year' => $year,
                        'observations' => 0,
                        'species_set' => [],
                    ];
                }
                $yearlyTimeseries[$year]['observations']++;
            }

            $protectedAreaName = trim((string) (ObservationRowValue::field($row, 'protected_area_name') ?? ''));
            $areaKey = $protectedAreaName !== '' ? $protectedAreaName : 'Unspecified Protected Area';
            if (! isset($topAreas[$areaKey])) {
                $topAreas[$areaKey] = [
                    'label' => $areaKey,
                    'observation_count' => 0,
                    'recorded_count_sum' => 0,
                    'species_set' => [],
                ];
            }
            $topAreas[$areaKey]['observation_count']++;
            $topAreas[$areaKey]['recorded_count_sum'] += $recordedCount;

            $scientificName = trim((string) (ObservationRowValue::field($row, 'scientific_name') ?? ''));
            $commonName = trim((string) (ObservationRowValue::field($row, 'common_name') ?? ''));
            $speciesKey = $scientificName !== '' ? mb_strtolower($scientificName) : mb_strtolower($commonName);

            if ($speciesKey === '') {
                $speciesKey = 'unspecified-species';
            }

            if (! isset($topSpecies[$speciesKey])) {
                $topSpecies[$speciesKey] = [
                    'common_name' => $commonName !== '' ? $commonName : 'Unspecified',
                    'scientific_name' => $scientificName,
                    'observation_count' => 0,
                    'recorded_count_sum' => 0,
                    'period_counts' => [],
                ];
            }
            $topSpecies[$speciesKey]['observation_count']++;
            $topSpecies[$speciesKey]['recorded_count_sum'] += $recordedCount;
            if ($periodKey !== null) {
                $topSpecies[$speciesKey]['period_counts'][$periodKey] = ($topSpecies[$speciesKey]['period_counts'][$periodKey] ?? 0) + 1;
            }

            if ($scientificName !== '') {
                if ($year > 0) {
                    $yearlyTimeseries[$year]['species_set'][mb_strtolower($scientificName)] = true;
                }
                if ($periodKey !== null) {
                    $timeseries[$periodKey]['species_set'][mb_strtolower($scientificName)] = true;
                    $speciesPeriodMatrix[$periodKey][mb_strtolower($scientificName)] = true;
                }

                $scientificKey = mb_strtolower($scientificName);
                if (($speciesLookup[$scientificKey]['is_endemic'] ?? false) === true) {
                    $summaryStats['endemic_observations']++;
                }
                if (($speciesLookup[$scientificKey]['is_migratory'] ?? false) === true) {
                    $summaryStats['migratory_observations']++;
                }
                $topAreas[$areaKey]['species_set'][$scientificKey] = true;
            } else {
                $missingScientificNameCount++;
                if (count($missingScientificRows) < 10) {
                    $missingScientificRows[] = [
                        'protected_area_name' => $areaKey,
                        'station_code' => trim((string) (ObservationRowValue::field($row, 'station_code') ?? '')) ?: 'N/A',
                        'common_name' => $commonName !== '' ? $commonName : 'Unspecified',
                    ];
                }
            }

            $bioGroup = trim((string) (ObservationRowValue::field($row, 'bio_group') ?? ''));
            $bioGroupKey = $bioGroup !== '' ? strtolower($bioGroup) : 'unspecified';
            if (! isset($bioGroupBreakdown[$bioGroupKey])) {
                $bioGroupBreakdown[$bioGroupKey] = [
                    'label' => ucfirst($bioGroupKey),
                    'observation_count' => 0,
                    'recorded_count_sum' => 0,
                ];
            }
            $bioGroupBreakdown[$bioGroupKey]['observation_count']++;
            $bioGroupBreakdown[$bioGroupKey]['recorded_count_sum'] += $recordedCount;

            $stationCode = trim((string) (ObservationRowValue::field($row, 'station_code') ?? ''));
            if ($stationCode !== '') {
                if (! array_key_exists($stationCode, $stationMappingCache)) {
                    $stationMappingCache[$stationCode] = SiteName::findByStationCode($stationCode) !== null;
                }
            }
            if ($stationCode !== '' && $stationMappingCache[$stationCode] === false) {
                $unknownStationCodes[$stationCode] = ($unknownStationCodes[$stationCode] ?? 0) + 1;
            }
        }

        usort($topAreas, static function (array $a, array $b): int {
            return $b['observation_count'] <=> $a['observation_count'];
        });

        usort($topSpecies, static function (array $a, array $b): int {
            if ($a['recorded_count_sum'] !== $b['recorded_count_sum']) {
                return $b['recorded_count_sum'] <=> $a['recorded_count_sum'];
            }

            return $b['observation_count'] <=> $a['observation_count'];
        });

        usort($bioGroupBreakdown, static function (array $a, array $b): int {
            return $b['observation_count'] <=> $a['observation_count'];
        });

        usort($timeseries, static function (array $a, array $b): int {
            if ($a['year'] !== $b['year']) {
                return $a['year'] <=> $b['year'];
            }

            return $a['semester'] <=> $b['semester'];
        });

        $timeseries = array_map(static function (array $item): array {
            return [
                'label' => $item['label'],
                'year' => $item['year'],
                'semester' => $item['semester'],
                'observation_count' => $item['observation_count'],
                'recorded_count_sum' => $item['recorded_count_sum'],
                'species_count' => count($item['species_set']),
            ];
        }, $timeseries);
        $timeseries = array_values($timeseries);

        ksort($yearlyTimeseries);
        $yearlyTimeseries = array_values(array_map(static function (array $item): array {
            return [
                'year' => (int) $item['year'],
                'observations' => (int) $item['observations'],
                'species_tracked' => count($item['species_set']),
            ];
        }, $yearlyTimeseries));

        $timeseriesMeta = $this->buildTimeseriesMeta($timeseries);
        $summaryComparisons = $this->buildSummaryComparisons($timeseries);
        $insights = $this->buildInsightAlerts($timeseries, $topAreas, $speciesPeriodMatrix);

        arsort($unknownStationCodes);
        $unknownStationRows = [];
        foreach (array_slice($unknownStationCodes, 0, 10, true) as $code => $count) {
            $unknownStationRows[] = [
                'station_code' => $code,
                'observation_count' => $count,
            ];
        }

        $areaRows = array_values(array_map(static function (array $row): array {
            $row['species_count'] = count($row['species_set'] ?? []);
            unset($row['species_set']);
            return $row;
        }, $topAreas));
        usort($areaRows, static function (array $a, array $b): int {
            if ($a['species_count'] !== $b['species_count']) {
                return $b['species_count'] <=> $a['species_count'];
            }
            return $b['observation_count'] <=> $a['observation_count'];
        });
        $spatial = $this->buildSpatialInsights($areaRows);

        $speciesIntelligence = $this->buildSpeciesIntelligence($topSpecies, $speciesLookup);

        return [
            'summary' => $summaryStats,
            'summary_comparisons' => $summaryComparisons,
            'timeseries' => $timeseries,
            'yearly_timeseries' => $yearlyTimeseries,
            'timeseries_meta' => $timeseriesMeta,
            'top_areas' => array_slice($spatial['ranked_areas'], 0, 10),
            'spatial_insights' => $spatial,
            'top_species' => array_slice(array_values($topSpecies), 0, 10),
            'species_intelligence' => $speciesIntelligence,
            'bio_group_breakdown' => array_values($bioGroupBreakdown),
            'insight_alerts' => $insights,
            'quality' => [
                'missing_scientific_name_count' => $missingScientificNameCount,
                'unknown_station_code_count' => count($unknownStationCodes),
                'unknown_station_rows' => $unknownStationRows,
                'missing_scientific_rows' => $missingScientificRows,
            ],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $timeseries
     * @return array<string, mixed>
     */
    private function buildTimeseriesMeta(array $timeseries): array
    {
        if (count($timeseries) === 0) {
            return ['anomaly_periods' => []];
        }

        $obsValues = array_map(static fn (array $row): int => (int) ($row['observation_count'] ?? 0), $timeseries);
        $mean = array_sum($obsValues) / max(count($obsValues), 1);
        $variance = 0.0;
        foreach ($obsValues as $value) {
            $variance += ($value - $mean) ** 2;
        }
        $stdDev = sqrt($variance / max(count($obsValues), 1));

        $anomalies = [];
        foreach ($timeseries as $index => $row) {
            $value = (int) ($row['observation_count'] ?? 0);
            $isOutlier = $stdDev > 0 && abs($value - $mean) >= ($stdDev * 1.5);
            $prev = $timeseries[$index - 1]['observation_count'] ?? null;
            $isShock = $prev !== null && $prev > 0 && abs(($value - $prev) / $prev) >= 0.45;
            if ($isOutlier || $isShock) {
                $anomalies[] = [
                    'label' => (string) ($row['label'] ?? ''),
                    'observation_count' => $value,
                ];
            }
        }

        return ['anomaly_periods' => $anomalies];
    }

    /**
     * @param  array<int, array<string, mixed>>  $timeseries
     * @return array<string, array<string, float|int|null|string>>
     */
    private function buildSummaryComparisons(array $timeseries): array
    {
        $current = $timeseries[count($timeseries) - 1] ?? null;
        $previous = $timeseries[count($timeseries) - 2] ?? null;

        $makeDelta = static function (?int $currentValue, ?int $previousValue): array {
            if ($currentValue === null || $previousValue === null) {
                return ['direction' => 'neutral', 'percent' => null, 'label' => 'No baseline'];
            }
            if ($previousValue === 0) {
                if ($currentValue === 0) {
                    return ['direction' => 'neutral', 'percent' => 0.0, 'label' => 'No change'];
                }
                return ['direction' => 'up', 'percent' => null, 'label' => 'New activity'];
            }

            $change = (($currentValue - $previousValue) / $previousValue) * 100;
            $direction = $change > 0 ? 'up' : ($change < 0 ? 'down' : 'neutral');
            return [
                'direction' => $direction,
                'percent' => round($change, 1),
                'label' => $direction === 'neutral' ? 'No change' : abs(round($change, 1)).'% '.($direction === 'up' ? 'increase' : 'decrease'),
            ];
        };

        return [
            'period_label' => $current['label'] ?? 'Current period',
            'baseline_label' => $previous['label'] ?? 'Previous period',
            'total_observations' => $makeDelta(
                isset($current['observation_count']) ? (int) $current['observation_count'] : null,
                isset($previous['observation_count']) ? (int) $previous['observation_count'] : null
            ),
            'total_recorded_count' => $makeDelta(
                isset($current['recorded_count_sum']) ? (int) $current['recorded_count_sum'] : null,
                isset($previous['recorded_count_sum']) ? (int) $previous['recorded_count_sum'] : null
            ),
            'total_species' => $makeDelta(
                isset($current['species_count']) ? (int) $current['species_count'] : null,
                isset($previous['species_count']) ? (int) $previous['species_count'] : null
            ),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $areaRows
     * @return array<string, mixed>
     */
    private function buildSpatialInsights(array $areaRows): array
    {
        if (count($areaRows) === 0) {
            return [
                'ranked_areas' => [],
                'hotspots' => [],
                'underreported_areas' => [],
            ];
        }

        $obsMean = array_sum(array_column($areaRows, 'observation_count')) / max(count($areaRows), 1);
        $speciesMean = array_sum(array_column($areaRows, 'species_count')) / max(count($areaRows), 1);
        $ranked = [];

        foreach ($areaRows as $row) {
            $isHotspot = $row['observation_count'] >= $obsMean && $row['species_count'] >= $speciesMean;
            $isUnderreported = $row['observation_count'] < max(5, (int) floor($obsMean * 0.4));
            $row['is_hotspot'] = $isHotspot;
            $row['is_underreported'] = $isUnderreported;
            $ranked[] = $row;
        }

        return [
            'ranked_areas' => $ranked,
            'hotspots' => array_values(array_slice(array_filter($ranked, static fn (array $item): bool => $item['is_hotspot'] === true), 0, 5)),
            'underreported_areas' => array_values(array_slice(array_filter($ranked, static fn (array $item): bool => $item['is_underreported'] === true), 0, 5)),
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $topSpecies
     * @param  array<string, array<string, mixed>>  $speciesLookup
     * @return array<string, mixed>
     */
    private function buildSpeciesIntelligence(array $topSpecies, array $speciesLookup): array
    {
        $rows = [];
        foreach ($topSpecies as $row) {
            $scientific = mb_strtolower(trim((string) ($row['scientific_name'] ?? '')));
            $registry = $scientific !== '' ? ($speciesLookup[$scientific] ?? null) : null;
            $periodCounts = array_values((array) ($row['period_counts'] ?? []));
            $current = (int) ($periodCounts[count($periodCounts) - 1] ?? 0);
            $previous = (int) ($periodCounts[count($periodCounts) - 2] ?? 0);
            $direction = $current > $previous ? 'up' : ($current < $previous ? 'down' : 'flat');
            $rows[] = [
                'common_name' => $row['common_name'],
                'scientific_name' => $row['scientific_name'],
                'observation_count' => (int) $row['observation_count'],
                'recorded_count_sum' => (int) $row['recorded_count_sum'],
                'conservation_status' => $this->humanizeStatus((string) ($registry['conservation_status'] ?? '')),
                'trend_direction' => $direction,
                'trend_delta' => $current - $previous,
            ];
        }

        $mostObserved = $rows;
        usort($mostObserved, static fn (array $a, array $b): int => $b['observation_count'] <=> $a['observation_count']);
        $highestCount = $rows;
        usort($highestCount, static fn (array $a, array $b): int => $b['recorded_count_sum'] <=> $a['recorded_count_sum']);
        $declining = array_values(array_filter($rows, static fn (array $item): bool => $item['trend_direction'] === 'down'));
        usort($declining, static fn (array $a, array $b): int => $a['trend_delta'] <=> $b['trend_delta']);

        return [
            'most_observed' => array_slice($mostObserved, 0, 10),
            'highest_count' => array_slice($highestCount, 0, 10),
            'declining' => array_slice($declining, 0, 10),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $timeseries
     * @param  array<string, array<string, mixed>>  $topAreas
     * @param  array<string, array<string, bool>>  $speciesPeriodMatrix
     * @return array<int, array<string, string>>
     */
    private function buildInsightAlerts(array $timeseries, array $topAreas, array $speciesPeriodMatrix): array
    {
        $alerts = [];
        $current = $timeseries[count($timeseries) - 1] ?? null;
        $previous = $timeseries[count($timeseries) - 2] ?? null;

        if ($current !== null && $previous !== null) {
            $prevObs = (int) ($previous['observation_count'] ?? 0);
            $currObs = (int) ($current['observation_count'] ?? 0);
            if ($prevObs > 0 && $currObs < ($prevObs * 0.6)) {
                $alerts[] = [
                    'level' => 'high',
                    'message' => 'Sharp drop in observations this period ('.$previous['label'].' to '.$current['label'].').',
                ];
            }
            if ($prevObs > 0 && $currObs > ($prevObs * 1.45)) {
                $alerts[] = [
                    'level' => 'medium',
                    'message' => 'Unusual spike in observations this period ('.$previous['label'].' to '.$current['label'].').',
                ];
            }
        }

        if (count($topAreas) > 0) {
            uasort($topAreas, static fn (array $a, array $b): int => $b['observation_count'] <=> $a['observation_count']);
            $topArea = array_values($topAreas)[0] ?? null;
            if ($topArea !== null) {
                $alerts[] = [
                    'level' => 'info',
                    'message' => 'Hotspot: '.$topArea['label'].' leads with '.number_format((int) $topArea['observation_count']).' observations.',
                ];
            }
        }

        if (count($speciesPeriodMatrix) >= 2) {
            $periodKeys = array_keys($speciesPeriodMatrix);
            sort($periodKeys);
            $currentKey = $periodKeys[count($periodKeys) - 1];
            $previousKey = $periodKeys[count($periodKeys) - 2];
            $missingNow = array_diff(array_keys($speciesPeriodMatrix[$previousKey] ?? []), array_keys($speciesPeriodMatrix[$currentKey] ?? []));
            if (count($missingNow) > 0) {
                $alerts[] = [
                    'level' => 'medium',
                    'message' => count($missingNow).' previously recorded species are not observed in the current period.',
                ];
            }
        }

        return array_slice($alerts, 0, 5);
    }

    private function humanizeStatus(string $status): string
    {
        $status = trim($status);
        if ($status === '') {
            return 'Unknown';
        }
        return ucwords(str_replace('_', ' ', $status));
    }
}
