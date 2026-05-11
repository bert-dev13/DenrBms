<?php

namespace Tests\Unit;

use App\Data\ObservationFactFilter;
use Illuminate\Http\Request;
use Tests\TestCase;

class ObservationFactFilterTest extends TestCase
{
    public function test_species_ranking_style_preserves_supported_filters(): void
    {
        $request = Request::create('/reports/species-ranking', 'GET', [
            'protected_area_id' => '5',
            'site_name' => '42',
            'patrol_year' => '2026',
            'search' => 'eagle',
        ]);

        $filter = ObservationFactFilter::fromSpeciesObservationStyleRequest($request);

        $this->assertEquals(
            [
                'protected_area_id' => '5',
                'site_name' => '42',
                'patrol_year' => '2026',
                'search' => 'eagle',
            ],
            $filter->unionQueryParams
        );
    }

    public function test_species_observation_style_preserves_site_name(): void
    {
        $request = Request::create('/species-observations', 'GET', [
            'protected_area_id' => '1',
            'site_name' => '99',
            'bio_group' => 'fauna',
        ]);

        $filter = ObservationFactFilter::fromSpeciesObservationStyleRequest($request);

        $this->assertSame([
            'protected_area_id' => '1',
            'site_name' => '99',
            'bio_group' => 'fauna',
        ], $filter->unionQueryParams);
    }

    public function test_species_observation_style_preserves_middleware_merged_filters(): void
    {
        $request = Request::create('/species-observations', 'GET', []);
        $request->merge(['protected_area_id' => '7']);

        $filter = ObservationFactFilter::fromSpeciesObservationStyleRequest($request);

        $this->assertSame([
            'protected_area_id' => '7',
        ], $filter->unionQueryParams);
    }

    public function test_to_union_request_preserves_url(): void
    {
        $original = Request::create('https://example.test/reports/foo', 'GET', []);
        $filter = new ObservationFactFilter(['protected_area_id' => '3']);

        $sub = $filter->toUnionRequest($original);

        $this->assertSame('https://example.test/reports/foo', $sub->url());
        $this->assertSame('3', $sub->query('protected_area_id'));
    }
}
