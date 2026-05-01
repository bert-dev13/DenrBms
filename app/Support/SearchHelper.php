<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SearchHelper
{
    /**
     * Apply case-insensitive search against a collection.
     *
     * @param Collection<int, mixed> $collection
     * @param string $term
     * @param array<int, callable(mixed): string> $extractors
     * @return Collection<int, mixed>
     */
    public static function filterCollection(Collection $collection, string $term, array $extractors): Collection
    {
        $needle = strtolower(trim($term));
        if ($needle === '') {
            return $collection->values();
        }

        return $collection->filter(function ($item) use ($needle, $extractors) {
            foreach ($extractors as $extractor) {
                $value = strtolower((string) $extractor($item));
                if ($value !== '' && str_contains($value, $needle)) {
                    return true;
                }
            }

            return false;
        })->values();
    }

    /**
     * Apply schema-safe OR search on existing table columns only.
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param string $tableName
     * @param string $term
     * @param array<int, string> $columns
     * @param null|callable($query, string): void $extraOrConditions
     * @return void
     */
    public static function applySafeColumnSearch($query, string $tableName, string $term, array $columns, ?callable $extraOrConditions = null): void
    {
        $searchTerm = trim($term);
        if ($searchTerm === '') {
            return;
        }

        $query->where(function ($q) use ($tableName, $searchTerm, $columns, $extraOrConditions) {
            $hasAnySearchColumn = false;

            foreach ($columns as $column) {
                if (!Schema::hasColumn($tableName, $column)) {
                    continue;
                }

                if (!$hasAnySearchColumn) {
                    $q->where($column, 'like', '%' . $searchTerm . '%');
                    $hasAnySearchColumn = true;
                } else {
                    $q->orWhere($column, 'like', '%' . $searchTerm . '%');
                }
            }

            if ($extraOrConditions) {
                $extraOrConditions($q, $searchTerm);
            }
        });
    }
}

