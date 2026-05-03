<?php

namespace App\Services;

use App\Data\ObservationFactFilter;
use App\Models\ProtectedArea;
use App\Models\SiteName;
use App\Models\Species;
use App\Support\ObservationRowValue;
use Illuminate\Http\Request;

final class AnalyticsService
{
    public function __construct(
        private SpeciesObservationFactService $observationFactService,
        private SpeciesCanonicalResolver $speciesCanonicalResolver,
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
        $threatenedSpecies = [];
        $bioGroupBreakdown = [];
        $conservationStatusBreakdown = [
            'critically_endangered' => ['label' => 'Critically Endangered', 'observation_count' => 0],
            'endangered' => ['label' => 'Endangered', 'observation_count' => 0],
            'vulnerable' => ['label' => 'Vulnerable', 'observation_count' => 0],
            'least_concern' => ['label' => 'Least Concern', 'observation_count' => 0],
        ];
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
            $speciesId = ObservationRowValue::field($row, 'species_id');
            $resolvedSpecies = $this->speciesCanonicalResolver->resolve(
                $scientificName,
                $commonName,
                is_numeric($speciesId) ? (int) $speciesId : null
            );
            $speciesKey = (string) $resolvedSpecies['key'];
            $canonicalCommonName = (string) $resolvedSpecies['common_name'];
            $canonicalScientificName = (string) $resolvedSpecies['scientific_name'];
            /** @var \App\Models\Species|null $matchedSpecies */
            $matchedSpecies = $resolvedSpecies['species'];

            if (! isset($topSpecies[$speciesKey])) {
                $topSpecies[$speciesKey] = [
                    'common_name' => $canonicalCommonName,
                    'scientific_name' => $canonicalScientificName,
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

            if ($speciesKey !== 'raw:unspecified') {
                if ($year > 0) {
                    $yearlyTimeseries[$year]['species_set'][$speciesKey] = true;
                }
                if ($periodKey !== null) {
                    $timeseries[$periodKey]['species_set'][$speciesKey] = true;
                    $speciesPeriodMatrix[$periodKey][$speciesKey] = true;
                }

                $scientificKey = mb_strtolower($scientificName);
                $isEndemic = $matchedSpecies?->is_endemic ?? ($speciesLookup[$scientificKey]['is_endemic'] ?? false);
                $isMigratory = $matchedSpecies?->is_migratory ?? ($speciesLookup[$scientificKey]['is_migratory'] ?? false);
                $resolvedStatus = $matchedSpecies?->conservation_status ?? ($speciesLookup[$scientificKey]['conservation_status'] ?? '');

                if ($isEndemic === true) {
                    $summaryStats['endemic_observations']++;
                }
                if ($isMigratory === true) {
                    $summaryStats['migratory_observations']++;
                }
                $statusKey = $this->normalizeConservationStatus((string) $resolvedStatus);
                if ($statusKey !== null) {
                    $conservationStatusBreakdown[$statusKey]['observation_count']++;
                    if (in_array($statusKey, ['critically_endangered', 'endangered', 'vulnerable'], true)) {
                        if (! isset($threatenedSpecies[$speciesKey])) {
                            $threatenedSpecies[$speciesKey] = [
                                'common_name' => $canonicalCommonName,
                                'scientific_name' => $canonicalScientificName,
                                'threatened_observation_count' => 0,
                            ];
                        }
                        $threatenedSpecies[$speciesKey]['threatened_observation_count']++;
                    }
                }
                $topAreas[$areaKey]['species_set'][$speciesKey] = true;
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
        usort($threatenedSpecies, static function (array $a, array $b): int {
            return ((int) ($b['threatened_observation_count'] ?? 0)) <=> ((int) ($a['threatened_observation_count'] ?? 0));
        });
        $summaryStats['total_species'] = count(array_filter(array_keys($topSpecies), static fn (string $k): bool => $k !== 'raw:unspecified'));

        return [
            'summary' => $summaryStats,
            'summary_comparisons' => $summaryComparisons,
            'timeseries' => $timeseries,
            'yearly_timeseries' => $yearlyTimeseries,
            'timeseries_meta' => $timeseriesMeta,
            'top_areas' => array_slice($spatial['ranked_areas'], 0, 10),
            'spatial_insights' => $spatial,
            'top_species' => array_slice(array_values($topSpecies), 0, 10),
            'top_threatened_species' => array_slice(array_values($threatenedSpecies), 0, 10),
            'species_intelligence' => $speciesIntelligence,
            'bio_group_breakdown' => array_values($bioGroupBreakdown),
            'conservation_status_breakdown' => array_values($conservationStatusBreakdown),
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
     * @return array<string, mixed>
     */
    public function buildSpeciesAnalyticsDataset(Request $request): array
    {
        $filter = ObservationFactFilter::fromSpeciesObservationStyleRequest($request);
        $facts = $this->observationFactService->getFactsWithSummary($filter, $request);
        $rows = $facts['rows'];

        $yearlySpeciesSet = [];
        $yearlyTotalObservations = [];
        $yearlyTotalRecordedCounts = [];
        $speciesTotals = [];
        $speciesYearlyCounts = [];
        $speciesYearlyObservationCounts = [];
        $speciesAreaYearlyCounts = [];
        $speciesLabels = [];
        $allYearsSet = [];
        foreach ($rows as $row) {
            $year = (int) (ObservationRowValue::field($row, 'patrol_year') ?? 0);
            if ($year <= 0) {
                continue;
            }
            $allYearsSet[$year] = true;
            $recordedCount = ObservationRowValue::recordedCount($row);
            $yearlyTotalObservations[$year] = ($yearlyTotalObservations[$year] ?? 0) + 1;
            $yearlyTotalRecordedCounts[$year] = ($yearlyTotalRecordedCounts[$year] ?? 0) + $recordedCount;

            $scientificName = trim((string) (ObservationRowValue::field($row, 'scientific_name') ?? ''));
            $commonName = trim((string) (ObservationRowValue::field($row, 'common_name') ?? ''));
            $speciesId = ObservationRowValue::field($row, 'species_id');
            $resolvedSpecies = $this->speciesCanonicalResolver->resolve(
                $scientificName,
                $commonName,
                is_numeric($speciesId) ? (int) $speciesId : null
            );
            $speciesKey = (string) $resolvedSpecies['key'];
            if ($speciesKey === 'raw:unspecified') {
                continue;
            }

            $yearlySpeciesSet[$year][$speciesKey] = true;
            $speciesTotals[$speciesKey] = ($speciesTotals[$speciesKey] ?? 0) + $recordedCount;
            $speciesYearlyCounts[$speciesKey][$year] = ($speciesYearlyCounts[$speciesKey][$year] ?? 0) + $recordedCount;
            $speciesYearlyObservationCounts[$speciesKey][$year] = ($speciesYearlyObservationCounts[$speciesKey][$year] ?? 0) + 1;
            $protectedAreaName = trim((string) (ObservationRowValue::field($row, 'protected_area_name') ?? ''));
            $areaKey = $protectedAreaName !== '' ? $protectedAreaName : 'Unspecified Protected Area';
            $speciesAreaYearlyCounts[$speciesKey][$areaKey][$year] = ($speciesAreaYearlyCounts[$speciesKey][$areaKey][$year] ?? 0) + $recordedCount;
            if (! isset($speciesLabels[$speciesKey])) {
                $label = (string) $resolvedSpecies['common_name'];
                $speciesLabels[$speciesKey] = $label !== '' ? $label : 'Unspecified';
            }
        }

        ksort($yearlySpeciesSet);
        $yearlyTrend = [];
        foreach ($yearlySpeciesSet as $year => $speciesSet) {
            $yearlyTrend[] = [
                'year' => (int) $year,
                'distinct_species_count' => count($speciesSet),
            ];
        }
        ksort($yearlyTotalObservations);
        ksort($yearlyTotalRecordedCounts);
        $yearlyTotalCounts = [];
        foreach ($yearlyTotalObservations as $year => $totalCount) {
            $yearlyTotalCounts[] = [
                'year' => (int) $year,
                'total_observations' => (int) $totalCount,
                'total_recorded_count' => (int) ($yearlyTotalRecordedCounts[$year] ?? 0),
            ];
        }

        arsort($speciesTotals);
        $topSpeciesKeys = array_slice(array_keys($speciesTotals), 0, 20);
        $topSpeciesOptions = [];
        $speciesTrends = [];
        $allYears = array_keys($allYearsSet);
        sort($allYears);
        foreach ($topSpeciesKeys as $speciesKey) {
            $yearlyCounts = $speciesYearlyCounts[$speciesKey] ?? [];
            ksort($yearlyCounts);
            $trendRows = [];
            foreach ($allYears as $year) {
                $trendRows[] = [
                    'year' => (int) $year,
                    'recorded_count_sum' => (int) ($yearlyCounts[$year] ?? 0),
                ];
            }

            $topSpeciesOptions[] = [
                'species_key' => $speciesKey,
                'label' => $speciesLabels[$speciesKey] ?? 'Unspecified',
                'total_recorded_count' => (int) ($speciesTotals[$speciesKey] ?? 0),
            ];
            $speciesTrends[$speciesKey] = $trendRows;
        }

        // Trend rankings (Top Increasing/Decreasing) should follow active filters.
        $trendRows = $rows;

        $growthYearSet = [];
        $growthSpeciesYearlyRecordedCounts = [];
        $growthSpeciesLabels = [];
        foreach ($trendRows as $row) {
            $year = (int) (ObservationRowValue::field($row, 'patrol_year') ?? 0);
            if ($year <= 0) {
                continue;
            }

            $scientificName = trim((string) (ObservationRowValue::field($row, 'scientific_name') ?? ''));
            $commonName = trim((string) (ObservationRowValue::field($row, 'common_name') ?? ''));
            $speciesId = ObservationRowValue::field($row, 'species_id');
            $resolvedSpecies = $this->speciesCanonicalResolver->resolve(
                $scientificName,
                $commonName,
                is_numeric($speciesId) ? (int) $speciesId : null
            );
            $speciesKey = (string) $resolvedSpecies['key'];
            if ($speciesKey === 'raw:unspecified') {
                continue;
            }

            $recordedCount = ObservationRowValue::recordedCount($row);
            $growthYearSet[$year] = true;
            $growthSpeciesYearlyRecordedCounts[$speciesKey][$year] = ($growthSpeciesYearlyRecordedCounts[$speciesKey][$year] ?? 0) + $recordedCount;
            if (! isset($growthSpeciesLabels[$speciesKey])) {
                $label = (string) $resolvedSpecies['common_name'];
                $growthSpeciesLabels[$speciesKey] = $label !== '' ? $label : ($speciesLabels[$speciesKey] ?? 'Unspecified');
            }
        }

        $speciesGrowthRows = [];
        foreach ($growthSpeciesYearlyRecordedCounts as $speciesKey => $yearlyRecordedCounts) {
            ksort($yearlyRecordedCounts);
            $speciesYears = array_keys($yearlyRecordedCounts);
            if (count($speciesYears) < 2) {
                continue;
            }

            $speciesEarliestYear = (int) $speciesYears[0];
            $speciesLatestYear = (int) $speciesYears[count($speciesYears) - 1];
            $earliestValue = (int) ($yearlyRecordedCounts[$speciesEarliestYear] ?? 0);
            $latestValue = (int) ($yearlyRecordedCounts[$speciesLatestYear] ?? 0);
            $delta = $latestValue - $earliestValue;
            $speciesGrowthRows[] = [
                'species_key' => $speciesKey,
                'label' => $growthSpeciesLabels[$speciesKey] ?? ($speciesLabels[$speciesKey] ?? 'Unspecified'),
                'delta' => $delta,
                'delta_abs' => abs($delta),
                'earliest_year' => $speciesEarliestYear,
                'latest_year' => $speciesLatestYear,
                'latest_value' => $latestValue,
                'total_recorded_count' => (int) array_sum($yearlyRecordedCounts),
            ];
        }

        $topIncreasingSpecies = array_values(array_filter($speciesGrowthRows, static fn (array $item): bool => (int) $item['delta'] > 0));
        usort($topIncreasingSpecies, static function (array $a, array $b): int {
            if (((int) $b['delta']) !== ((int) $a['delta'])) {
                return ((int) $b['delta']) <=> ((int) $a['delta']);
            }
            if (((int) $b['latest_value']) !== ((int) $a['latest_value'])) {
                return ((int) $b['latest_value']) <=> ((int) $a['latest_value']);
            }
            return ((int) $b['total_recorded_count']) <=> ((int) $a['total_recorded_count']);
        });
        $topIncreasingSpecies = array_slice($topIncreasingSpecies, 0, 10);

        $topDecreasingSpecies = array_values(array_filter($speciesGrowthRows, static fn (array $item): bool => (int) $item['delta'] < 0));
        usort($topDecreasingSpecies, static function (array $a, array $b): int {
            if (((int) $a['delta']) !== ((int) $b['delta'])) {
                return ((int) $a['delta']) <=> ((int) $b['delta']);
            }
            if (((int) $b['latest_value']) !== ((int) $a['latest_value'])) {
                return ((int) $b['latest_value']) <=> ((int) $a['latest_value']);
            }
            return ((int) $b['total_recorded_count']) <=> ((int) $a['total_recorded_count']);
        });
        $topDecreasingSpecies = array_map(static function (array $item): array {
            $delta = (int) $item['delta'];
            $item['decline_abs'] = abs($delta);
            $item['delta_abs'] = $item['decline_abs'];
            return $item;
        }, array_slice($topDecreasingSpecies, 0, 10));

        $requestedSpeciesKey = mb_strtolower(trim((string) $request->input('species_key', '')));
        $selectedSpeciesKey = $requestedSpeciesKey !== '' && isset($speciesTrends[$requestedSpeciesKey])
            ? $requestedSpeciesKey
            : ($topSpeciesKeys[0] ?? null);
        $selectedSpeciesTrend = $selectedSpeciesKey !== null ? ($speciesTrends[$selectedSpeciesKey] ?? []) : [];
        $selectedDirection = 'no_data';
        if (count($selectedSpeciesTrend) >= 2) {
            $last = (int) ($selectedSpeciesTrend[count($selectedSpeciesTrend) - 1]['recorded_count_sum'] ?? 0);
            $previous = (int) ($selectedSpeciesTrend[count($selectedSpeciesTrend) - 2]['recorded_count_sum'] ?? 0);
            $selectedDirection = $last > $previous ? 'increasing' : ($last < $previous ? 'decreasing' : 'flat');
        } elseif (count($selectedSpeciesTrend) === 1) {
            $selectedDirection = 'flat';
        }

        $requestedAreaSpeciesKey = mb_strtolower(trim((string) $request->input('pa_species_key', '')));
        $selectedAreaSpeciesKey = $requestedAreaSpeciesKey !== '' && isset($speciesAreaYearlyCounts[$requestedAreaSpeciesKey])
            ? $requestedAreaSpeciesKey
            : $selectedSpeciesKey;

        sort($allYears);
        $speciesAreaTrendsByKey = [];
        foreach ($speciesAreaYearlyCounts as $speciesKey => $areaRows) {
            $seriesRows = [];
            ksort($areaRows);
            foreach ($areaRows as $areaLabel => $yearlyCounts) {
                $points = [];
                foreach ($allYears as $year) {
                    $points[] = [
                        'year' => (int) $year,
                        'recorded_count_sum' => (int) ($yearlyCounts[$year] ?? 0),
                    ];
                }
                $seriesRows[] = [
                    'area_label' => (string) $areaLabel,
                    'points' => $points,
                ];
            }
            $speciesAreaTrendsByKey[$speciesKey] = $seriesRows;
        }

        $selectedAreaSpeciesTrends = [];
        if ($selectedAreaSpeciesKey !== null && isset($speciesAreaTrendsByKey[$selectedAreaSpeciesKey])) {
            $selectedAreaSpeciesTrends = $speciesAreaTrendsByKey[$selectedAreaSpeciesKey];
        }

        $selectedProtectedAreaId = $request->filled('protected_area_id')
            ? (int) $request->integer('protected_area_id')
            : null;
        $selectedProtectedAreaName = null;
        if ($selectedProtectedAreaId !== null && $selectedProtectedAreaId > 0) {
            $selectedProtectedAreaName = ProtectedArea::query()
                ->whereKey($selectedProtectedAreaId)
                ->value('name');
        }

        return [
            'yearly_species_trend' => $yearlyTrend,
            'filters' => [
                'protected_area_id' => $selectedProtectedAreaId,
                'protected_area_name' => $selectedProtectedAreaName,
            ],
            'meta' => [
                'total_years' => count($yearlyTrend),
                'latest_year' => count($yearlyTrend) > 0 ? $yearlyTrend[count($yearlyTrend) - 1]['year'] : null,
            ],
            'top_species_options' => $topSpeciesOptions,
            'species_trends' => $speciesTrends,
            'selected_species_key' => $selectedSpeciesKey,
            'selected_species_label' => $selectedSpeciesKey !== null ? ($speciesLabels[$selectedSpeciesKey] ?? 'Unspecified') : null,
            'selected_species_direction' => $selectedDirection,
            'top_increasing_species' => $topIncreasingSpecies,
            'top_decreasing_species' => $topDecreasingSpecies,
            'yearly_total_counts' => $yearlyTotalCounts,
            'selected_area_species_key' => $selectedAreaSpeciesKey,
            'selected_area_species_label' => $selectedAreaSpeciesKey !== null ? ($speciesLabels[$selectedAreaSpeciesKey] ?? 'Unspecified') : null,
            'selected_area_species_trends' => $selectedAreaSpeciesTrends,
            'species_area_trends_by_key' => $speciesAreaTrendsByKey,
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

    private function normalizeConservationStatus(string $status): ?string
    {
        $normalized = strtolower(trim($status));
        if ($normalized === '') {
            return null;
        }

        // Make status matching resilient to formats like:
        // "Critically Endangered (CR)", "EN - Endangered", "Least Concern/LC".
        $compact = str_replace(['-', '_', '/', '(', ')', '[', ']', ','], ' ', $normalized);
        $compact = preg_replace('/\s+/', ' ', $compact) ?? $compact;
        $compact = trim($compact);

        if (str_contains($compact, 'critically endangered') || preg_match('/\bcr\b/', $compact) === 1) {
            return 'critically_endangered';
        }
        if (str_contains($compact, 'endangered') || preg_match('/\ben\b/', $compact) === 1) {
            return 'endangered';
        }
        if (str_contains($compact, 'vulnerable') || preg_match('/\bvu\b/', $compact) === 1) {
            return 'vulnerable';
        }
        if (str_contains($compact, 'least concern') || preg_match('/\blc\b/', $compact) === 1) {
            return 'least_concern';
        }

        return null;
    }
}
