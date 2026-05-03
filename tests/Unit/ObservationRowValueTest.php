<?php

namespace Tests\Unit;

use App\Support\ObservationRowValue;
use Tests\TestCase;

class ObservationRowValueTest extends TestCase
{
    public function test_recorded_count_parses_numeric_and_clamps_negative_roundtrip(): void
    {
        $row = (object) ['recorded_count' => '3.6'];
        $this->assertSame(4, ObservationRowValue::recordedCount($row));

        $this->assertSame(0, ObservationRowValue::recordedCount((object) ['recorded_count' => null]));
        $this->assertSame(0, ObservationRowValue::recordedCount((object) []));
    }

    public function test_summary_stats_matches_kpi_definition(): void
    {
        $rows = collect([
            (object) ['protected_area_id' => 1, 'scientific_name' => 'A a', 'recorded_count' => 2],
            (object) ['protected_area_id' => 1, 'scientific_name' => 'B b', 'recorded_count' => 3],
            (object) ['protected_area_id' => 2, 'scientific_name' => '', 'recorded_count' => 1],
        ]);

        $stats = ObservationRowValue::summaryStats($rows);

        $this->assertSame(3, $stats['total_observations']);
        $this->assertSame(6, $stats['total_recorded_count']);
        $this->assertSame(2, $stats['total_protected_areas']);
        $this->assertSame(2, $stats['total_species']);
    }
}
