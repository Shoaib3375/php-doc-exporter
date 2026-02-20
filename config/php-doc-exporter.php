<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Tokens
    |--------------------------------------------------------------------------
    |
    | Configure your API tokens for document export authentication.
    | Leave empty if you don't need token-based security.
    |
    */

    'main_token' => env('PHP_DOC_EXPORTER_MAIN_TOKEN', ''),
    'safe_token' => env('PHP_DOC_EXPORTER_SAFE_TOKEN', ''),
];
