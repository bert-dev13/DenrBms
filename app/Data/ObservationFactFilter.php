<?php

namespace App\Data;

use Illuminate\Http\Request;

/**
 * Normalized filter payload for {@see \App\Services\SpeciesObservationFactService}.
 * All reports that must match Species Observations should build this (one place for site_id → site_name, etc.).
 */
final class ObservationFactFilter
{
    /**
     * @param  array<string, scalar|null>  $unionQueryParams  Keys understood by {@see \App\Services\SpeciesObservationUnionService}.
     */
    public function __construct(
        public readonly array $unionQueryParams,
    ) {}

    /**
     * Species Observations index / exports / species ranking: uses site_name, rank_order is ignored here.
     */
    public static function fromSpeciesObservationStyleRequest(Request $request): self
    {
        $params = [];
        foreach (['protected_area_id', 'site_name', 'bio_group', 'patrol_year', 'patrol_semester', 'search'] as $key) {
            if ($request->filled($key)) {
                $params[$key] = $request->query($key);
            }
        }

        return new self($params);
    }

    /**
     * Endemic report: site_id selects site_names.id; union expects site_name.
     */
    public static function fromEndemicReportRequest(Request $request): self
    {
        $params = [];
        foreach (['protected_area_id', 'bio_group', 'patrol_year', 'patrol_semester', 'search'] as $key) {
            if ($request->filled($key)) {
                $params[$key] = $request->query($key);
            }
        }
        if ($request->filled('site_id')) {
            $params['site_name'] = $request->query('site_id');
        }

        return new self($params);
    }

    /**
     * Migratory species report: same query shape as {@see self::fromEndemicReportRequest()} (site_id → site_name).
     */
    public static function fromMigratoryReportRequest(Request $request): self
    {
        return self::fromEndemicReportRequest($request);
    }

    public function toUnionRequest(Request $original): Request
    {
        return Request::create($original->url(), 'GET', $this->unionQueryParams);
    }
}
