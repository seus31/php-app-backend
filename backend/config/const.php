<?php

use Illuminate\Support\Str;

return [
    'pagination' => [
        'page' => env('PAGINATION_PAGE', 1),
        'per_page' => env('PAGINATION_PER_PAGE', 15),
    ],
];
