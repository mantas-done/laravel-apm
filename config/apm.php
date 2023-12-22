<?php

return [
    'enabled' => env('APM', false),
    'per_page' => 100, // how many results per page to show
    'ignore' => [
        // put here any route names from the APM page to hide them
        'longest_requests' => [
            // 'apm',
        ],
    ],
];