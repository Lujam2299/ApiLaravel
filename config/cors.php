<?php

return [
    'paths' => ['api/*', 'broadcasting/*', 'reverb/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], 
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];