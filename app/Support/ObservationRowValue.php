<?php

namespace App\Support;

/**
 * Shared parsing of union / observation row fields (single definition for Σ).
 */
final class ObservationRowValue
{
    public static function recordedCount(object|array $row): int
    {
        $raw = self::field($row, 'recorded_count');
        if ($raw === null || $raw === '') {
            return 0;
        }
        if (is_numeric($raw)) {
            return max(0, (int) round((float) $raw));
        }

        return 0;
    }

    public static function field(object|array $row, string $key): mixed
    {
        if (is_array($row)) {
            return $row[$key] ?? null;
        }

        return $row->{$key} ?? null;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object|array>  $rows
     * @return array{total_observations: int, total_recorded_count: int, total_protected_areas: int, total_species: int}
     */
    public static function summaryStats(\Illuminate\Support\Collection $rows): array
    {
        return [
            'total_observations' => $rows->count(),
            'total_recorded_count' => $rows->sum(fn ($r) => self::recordedCount($r)),
            'total_protected_areas' => $rows->pluck('protected_area_id')->unique()->count(),
            'total_species' => $rows->pluck('scientific_name')->filter(fn ($v) => ! empty(trim((string) $v)))->unique()->count(),
        ];
    }
}
