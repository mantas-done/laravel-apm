<?php

return [
    'enabled' => env('APM', false),
    'per_page' => 100, // how many results per page to show
    'sampling' => 1, // logs only part of requests. 1 - 100%, 0.1 - 10% of requests.
    'ignore' => [
        // put here any route names from the APM page to hide them
        'longest_requests' => [
            // 'apm',
        ],
    ],
];