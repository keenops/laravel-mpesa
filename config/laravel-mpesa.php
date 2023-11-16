<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    // get this from your mpesa developer account
    'mpesa_api_key' => env('MPESA_API_KEY'),
    // get this from your mpesa developer account
    'mpesa_public_key' => env('MPESA_PUBLIC_KEY'),
    // options are: sandbox, openapi
    'mpesa_environment' => env('MPESA_ENVIROMENT'),
    // options are: TZN, GHA, LES, DRC
    'mpesa_market_country' => env('MPESA_MARKET_COUNTRY'),
    // options are: TZS, GHS, SLS, USD
    'mpesa_market_currency' => env('MPESA_MARKET_CURRENCY')
];