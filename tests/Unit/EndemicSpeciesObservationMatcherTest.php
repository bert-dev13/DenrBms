<?php

namespace Tests\Unit;

use App\Models\Species;
use App\Services\EndemicSpeciesObservationMatcher;
use Illuminate\Support\Collection;
use Tests\TestCase;

class EndemicSpeciesObservationMatcherTest extends TestCase
{
    public function test_resolves_by_scientific_name_then_common(): void
    {
        $s1 = new Species;
        $s1->forceFill(['id' => 1, 'name' => 'Common A', 'scientific_name' => 'Sci one']);

        $matcher = new EndemicSpeciesObservationMatcher;
        $maps = $matcher->buildLookupMaps(new Collection([$s1]));

        $this->assertSame($s1, $matcher->resolveSpecies((object) [
            'common_name' => 'wrong',
            'scientific_name' => 'Sci One',
        ], $maps['byScientific'], $maps['byCommon']));

        $this->assertSame($s1, $matcher->resolveSpecies((object) [
            'common_name' => 'Common A',
            'scientific_name' => '',
        ], $maps['byScientific'], $maps['byCommon']));

        $this->assertNull($matcher->resolveSpecies((object) [
            'common_name' => 'Other',
            'scientific_name' => 'Other',
        ], $maps['byScientific'], $maps['byCommon']));
    }
}
