<?php

namespace Tests\Unit;

use App\Data\ObservationFactFilter;
use Illuminate\Http\Request;
use Tests\TestCase;

class ObservationFactFilterTest extends TestCase
{
    public function test_endemic_maps_site_id_to_site_name_for_union(): void
    {
        $request = Request::create('/reports/endemic-species', 'GET', [
            'protected_area_id' => '5',
            'site_id' => '42',
            'search' => 'eagle',
        ]);

        $filter = ObservationFactFilter::fromEndemicReportRequest($request);

        $this->assertEquals(
            [
                'protected_area_id' => '5',
                'site_name' => '42',
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

    public function test_migratory_filter_matches_endemic_site_mapping(): void
    {
        $request = Request::create('/reports/migratory-species', 'GET', [
            'site_id' => '7',
            'protected_area_id' => '2',
        ]);

        $migratory = ObservationFactFilter::fromMigratoryReportRequest($request);
        $endemic = ObservationFactFilter::fromEndemicReportRequest($request);

        $this->assertEquals($endemic->unionQueryParams, $migratory->unionQueryParams);
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
