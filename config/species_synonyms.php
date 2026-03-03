<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Species name synonyms / alternate spellings → canonical form
    |--------------------------------------------------------------------------
    | Maps misspellings or variants to the authoritative scientific name.
    | Keys are normalized (lowercase) for matching; values are canonical.
    */

    'scientific_name' => [
        'centripus viridis'   => 'Centropus viridis',
        'centropus viridis'   => 'Centropus viridis',
        'zosterops meyeni'    => 'Zosterops meyeni',
        'macropygia tenuirostris' => 'Macropygia tenuirostris',
        'treron formosae'     => 'Treron formosae',
        'amaurornis olivacea' => 'Amaurornis olivacea',
        // Flora (typos / variants → canonical)
        'shorea concorta'     => 'Shorea contorta',
        'diospyrus pilosanthera' => 'Diospyros pilosanthera',
        'biscofia javanica'   => 'Bischofia javanica',
    ],

    'common_name' => [
        'philippine coucal'     => 'Philippine Coucal',
        'philippine cuckoo dove' => 'Philippine Cuckoo Dove',
        'lowland white-eye'    => 'Lowland White-eye',
        'whistling green pigeon' => 'Whistling Green Pigeon',
        'plain bush hen'       => 'Plain Bush Hen',
        // Flora (case/spelling variants)
        'red lauan'            => 'Red lauan',
        'white lauan'          => 'White lauan',
        'antipolo'             => 'Antipolo',
        'antiplolo'            => 'Antipolo',
    ],
];
