<?php

namespace Tests\Feature;

use App\Models\ProtectedArea;
use App\Models\SiteName;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SearchIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    public function test_protected_areas_index_applies_server_side_search_via_search_query_param(): void
    {
        ProtectedArea::create(['code' => 'AAA', 'name' => 'Alpha Area']);
        ProtectedArea::create(['code' => 'BBB', 'name' => 'Beta Area']);

        $response = $this->get('/protected-areas?search=Alpha');

        $response->assertOk();
        $response->assertSee('Alpha Area');
        $response->assertDontSee('Beta Area');
    }

    public function test_protected_area_sites_index_searches_by_site_name_and_protected_area_name(): void
    {
        $alpha = ProtectedArea::create(['code' => 'ALP', 'name' => 'Alpha Protected Area']);
        $beta = ProtectedArea::create(['code' => 'BET', 'name' => 'Beta Protected Area']);

        SiteName::create(['name' => 'Alpha Site One', 'protected_area_id' => $alpha->id]);
        SiteName::create(['name' => 'Beta Site One', 'protected_area_id' => $beta->id]);

        $response = $this->get('/protected-area-sites?search=Alpha');

        $response->assertOk();
        $response->assertSee('Alpha Site One');
        $response->assertDontSee('Beta Site One');
    }

    public function test_species_observations_search_is_server_side_and_persists_in_pagination_links(): void
    {
        $area = ProtectedArea::create(['code' => 'BPLS', 'name' => 'Batanes Protected Landscape']);

        for ($i = 1; $i <= 25; $i++) {
            DB::table('batanes_tbl')->insert([
                'protected_area_id' => $area->id,
                'transaction_code' => 'TX-' . $i,
                'station_code' => 'MATCH-STATION',
                'patrol_year' => 2026,
                'patrol_semester' => 1,
                'bio_group' => 'fauna',
                'common_name' => 'Searchable Species ' . $i,
                'scientific_name' => 'Searchus speciesus ' . $i,
                'recorded_count' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('batanes_tbl')->insert([
            'protected_area_id' => $area->id,
            'transaction_code' => 'TX-NO-MATCH',
            'station_code' => 'OTHER-STATION',
            'patrol_year' => 2026,
            'patrol_semester' => 1,
            'bio_group' => 'fauna',
            'common_name' => 'Different Species',
            'scientific_name' => 'Other species',
            'recorded_count' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get('/species-observations?search=MATCH-STATION');

        $response->assertOk();
        $response->assertSee('Searchable Species');
        $response->assertDontSee('Different Species');
        $response->assertSee('search=MATCH-STATION');
    }
}

