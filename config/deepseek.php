<?php

use DeepSeek\Enums\Configs\DefaultConfigs;

return [

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | The API key used to authenticate requests to the Deepseek API. This key
    | is required for all API interactions. Ensure that you set this value in
    | your environment file (.env) as DEEPSEEK_API_KEY to keep it secure.
    |
    */
    'api_key' => env('DEEPSEEK_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Deepseek API. By default, it uses the value defined
    | in DefaultConfigs::BASE_URL. You can override this by setting the
    | DEEPSEEK_API_BASE_URL variable in your environment file if you need to
    | connect to a custom endpoint.
    |
    */
    'base_url' => env('DEEPSEEK_API_BASE_URL', (string) DefaultConfigs::BASE_URL->value),

    /*
    |--------------------------------------------------------------------------
    | API Timeout
    |--------------------------------------------------------------------------
    |
    | The maximum time, in seconds, for API requests to complete before timing
    | out. This helps prevent your application from waiting indefinitely for
    | a response. The default value is taken from DefaultConfigs::TIMEOUT.
    | You can override it in the environment file by setting DEEPSEEK_API_TIMEOUT.
    |
    */
    'timeout' => env('DEEPSEEK_API_TIMEOUT', (int) DefaultConfigs::TIMEOUT->value),

];
