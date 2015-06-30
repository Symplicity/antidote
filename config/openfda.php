<?php

return [
    'api_base_uri' => 'https://api.fda.gov/',
    'rate_limit' => env('OPENFDA_RATE_LIMIT', 0),
    'api_key' => env('OPENFDA_API_KEY', false)
];
