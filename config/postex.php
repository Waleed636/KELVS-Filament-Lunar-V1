<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PostEx API Token
    |--------------------------------------------------------------------------
    |
    | This token is required in the headers of all API calls to PostEx to
    | identify and authorize your merchant account.
    |
    */
    'api_token' => env('POSTEX_API_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | PostEx API Base URL
    |--------------------------------------------------------------------------
    |
    | The main API server domain. The default production URL is:
    | https://api.postex.pk
    |
    */
    'base_url' => env('POSTEX_BASE_URL', 'https://api.postex.pk'),

    /*
    |--------------------------------------------------------------------------
    | PostEx Default Pickup Address Code
    |--------------------------------------------------------------------------
    |
    | Default code identifying the pickup location/warehouse in your PostEx
    | profile. This will be automatically pre-selected when booking orders.
    |
    */
    'default_pickup_address_code' => env('POSTEX_DEFAULT_PICKUP_ADDRESS_CODE'),

    /*
    |--------------------------------------------------------------------------
    | PostEx Default Order Type
    |--------------------------------------------------------------------------
    |
    | Default type for booked orders. Can be 'Normal', 'Reversed', or
    | 'Replacement'.
    |
    */
    'default_order_type' => env('POSTEX_DEFAULT_ORDER_TYPE', 'Normal'),

    /*
    |--------------------------------------------------------------------------
    | Verify SSL Certificates
    |--------------------------------------------------------------------------
    |
    | Whether to verify SSL certificates on HTTP requests. Useful to set to
    | false in local development environments with SSL configuration issues.
    |
    */
    'verify_ssl' => env('POSTEX_VERIFY_SSL', true),
];
