<?php

namespace App\Helpers;

/**
 * Centralized helper for Patrol Year options used across the application.
 * Years are generated dynamically from the current year, in descending order.
 */
class PatrolYearHelper
{
    /**
     * Default number of years to show (current year + past N years).
     */
    private const DEFAULT_YEAR_COUNT = 10;

    /**
     * Get configurable year count from config or use default.
     */
    public static function getYearCount(): int
    {
        return config('bms.patrol_year_count', self::DEFAULT_YEAR_COUNT);
    }

    /**
     * Get patrol years for dropdowns, filters, and forms.
     * Returns years in descending order from current year (e.g., 2026, 2025, 2024...).
     *
     * @param int|null $count Number of years to include (default: config or 10)
     * @return array<int> Years in descending order
     */
    public static function getYears(?int $count = null): array
    {
        $count = $count ?? self::getYearCount();
        $currentYear = (int) date('Y');

        $years = [];
        for ($year = $currentYear; $year >= $currentYear - $count + 1; $year--) {
            $years[] = $year;
        }

        return $years;
    }

    /**
     * Get the current year (default for new entries).
     */
    public static function getCurrentYear(): int
    {
        return (int) date('Y');
    }
}
