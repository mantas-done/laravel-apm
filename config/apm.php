<?php

return [
    'enabled' => env('APM', true),
    'per_page' => 100, // how many results per page to show
    'sampling' => 1, // logs only part of requests. 1 - 100%, 0.1 - 10% of requests.
    'slow' => 5, // log queries of pages that spent in SQL more than 10 seconds
];