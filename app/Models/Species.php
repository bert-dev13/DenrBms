<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Species extends Model
{
    protected $table = 'species';

    protected $fillable = [
        'name',
        'scientific_name',
        'is_endemic',
        'is_migratory',
        'conservation_status',
    ];

    protected $casts = [
        'is_endemic' => 'boolean',
        'is_migratory' => 'boolean',
    ];

    public function scopeEndemic(Builder $query): Builder
    {
        return $query->where('is_endemic', true);
    }

    public function scopeMigratory(Builder $query): Builder
    {
        return $query->where('is_migratory', true);
    }

    /**
     * Ensure shipped reporting defaults exist when the registry has no endemic rows yet
     * (e.g. migrate without db:seed). Idempotent via updateOrCreate on scientific_name.
     */
    public static function ensureReportingDefaultsWhenEmpty(): void
    {
        if (static::query()->endemic()->exists()) {
            return;
        }

        foreach (config('endemic_species_defaults', []) as $row) {
            if (empty($row['scientific_name'])) {
                continue;
            }

            static::updateOrCreate(
                ['scientific_name' => $row['scientific_name']],
                [
                    'name' => $row['name'],
                    'is_endemic' => (bool) ($row['is_endemic'] ?? true),
                    'is_migratory' => (bool) ($row['is_migratory'] ?? false),
                    'conservation_status' => $row['conservation_status'],
                ]
            );
        }
    }

    /**
     * Ensure shipped migratory reporting defaults exist when the registry has no migratory rows yet.
     */
    public static function ensureMigratoryReportingDefaultsWhenEmpty(): void
    {
        if (static::query()->migratory()->exists()) {
            return;
        }

        foreach (config('migratory_species_defaults', []) as $row) {
            if (empty($row['scientific_name'])) {
                continue;
            }

            static::updateOrCreate(
                ['scientific_name' => $row['scientific_name']],
                [
                    'name' => $row['name'],
                    'is_endemic' => (bool) ($row['is_endemic'] ?? false),
                    'is_migratory' => (bool) ($row['is_migratory'] ?? true),
                    'conservation_status' => $row['conservation_status'],
                ]
            );
        }
    }
}
