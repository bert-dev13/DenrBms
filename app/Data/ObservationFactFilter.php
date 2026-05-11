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
                $params[$key] = $request->input($key);
            }
        }

        return new self($params);
    }

    public function toUnionRequest(Request $original): Request
    {
        return Request::create($original->url(), 'GET', $this->unionQueryParams);
    }
}
