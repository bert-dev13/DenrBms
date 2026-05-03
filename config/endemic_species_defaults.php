<?php

/**
 * Default endemic species registry rows used by the Endemic Species Report and SpeciesSeeder.
 * Matched to observation common_name / scientific_name (after synonym normalization).
 */
return [
    [
        'name' => 'Lowland White-eye',
        'scientific_name' => 'Zosterops meyeni',
        'is_endemic' => true,
        'conservation_status' => 'vulnerable',
    ],
    [
        'name' => 'Philippine Cuckoo Dove',
        'scientific_name' => 'Macropygia tenuirostris',
        'is_endemic' => true,
        'conservation_status' => 'least_concern',
    ],
    [
        'name' => 'Brown-eared Bulbul',
        'scientific_name' => 'Hypsipetes amaurotis',
        'is_endemic' => true,
        'conservation_status' => 'least_concern',
    ],
    [
        'name' => 'Philippine Coucal',
        'scientific_name' => 'Centropus viridis',
        'is_endemic' => true,
        'conservation_status' => 'endangered',
    ],
    [
        'name' => 'Plain Bush Hen',
        'scientific_name' => 'Amaurornis olivacea',
        'is_endemic' => true,
        'conservation_status' => 'critically_endangered',
    ],
];
