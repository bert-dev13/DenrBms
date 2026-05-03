<?php

/**
 * Default migratory species registry rows for Migratory Species Report and SpeciesSeeder.
 * Matched to observation common_name / scientific_name (after synonym normalization).
 */
return [
    [
        'name' => 'Barn Swallow',
        'scientific_name' => 'Hirundo rustica',
        'is_endemic' => false,
        'is_migratory' => true,
        'conservation_status' => 'least_concern',
    ],
    [
        'name' => 'Paddyfield Pipit',
        'scientific_name' => 'Anthus rufulus',
        'is_endemic' => false,
        'is_migratory' => true,
        'conservation_status' => 'least_concern',
    ],
    [
        'name' => 'Zitting Cisticola',
        'scientific_name' => 'Cisticola juncidis',
        'is_endemic' => false,
        'is_migratory' => true,
        'conservation_status' => 'least_concern',
    ],
];
