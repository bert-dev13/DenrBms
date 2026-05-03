<?php

namespace Tests\Unit;

use App\Models\Species;
use App\Services\MigratorySpeciesObservationMatcher;
use Illuminate\Support\Collection;
use Tests\TestCase;

class MigratorySpeciesObservationMatcherTest extends TestCase
{
    public function test_resolves_migratory_species_by_name(): void
    {
        $s1 = new Species;
        $s1->forceFill(['id' => 2, 'name' => 'Arctic Tern', 'scientific_name' => 'Sterna paradisaea']);

        $matcher = new MigratorySpeciesObservationMatcher;
        $maps = $matcher->buildLookupMaps(new Collection([$s1]));

        $this->assertSame($s1, $matcher->resolveSpecies((object) [
            'common_name' => '',
            'scientific_name' => 'Sterna paradisaea',
        ], $maps['byScientific'], $maps['byCommon']));
    }
}
