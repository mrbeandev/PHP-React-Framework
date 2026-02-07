<?php

use App\Core\Support\Env;

return [
    'api_key' => Env::get('API_KEY', ''),
];
