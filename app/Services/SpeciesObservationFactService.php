<?php

namespace App\Services;

use App\Data\ObservationFactFilter;
use App\Support\ObservationRowValue;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Canonical observation "fact" rows for reporting: same multiset as Species Observations grid for a given {@see ObservationFactFilter}.
 */
final class SpeciesObservationFactService
{
    public function __construct(
        private SpeciesObservationUnionService $observationUnion,
    ) {}

    public function getFactRows(ObservationFactFilter $filter, Request $original): Collection
    {
        return $this->observationUnion->getUnionResults($filter->toUnionRequest($original));
    }

    /**
     * @return array{rows: Collection<int, object>, summaryStats: array<string, int>}
     */
    public function getFactsWithSummary(ObservationFactFilter $filter, Request $original): array
    {
        $rows = $this->getFactRows($filter, $original);

        return [
            'rows' => $rows,
            'summaryStats' => ObservationRowValue::summaryStats($rows),
        ];
    }
}
