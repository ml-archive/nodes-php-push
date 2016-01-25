<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | Set to true to use live application credentials. If set to false
    | the credentials of our internal test application will be used.
    |
    */
    'live' => env('PARSE_LIVE', true),

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | Name of queue to send push notifications from
    |
    */
    'queue' => null,

    /*
    |--------------------------------------------------------------------------
    | Provider
    |--------------------------------------------------------------------------
    |
    | Load your desired Push provider.
    |
    | Make sure your provider extends the push interface to make sure
    | that all the required methods has been implemented
    |
    */
    'provider' => function($app) {
        // Initiate Parse push handler
        $parse = new \Parse\ParsePush;

        // Instantiate Nodes Parse provider
        return new \Nodes\Push\Providers\Parse($parse, config('nodes.push.parse'), config('nodes.push.live'));
    },

    /*
    |--------------------------------------------------------------------------
    | Parse applications
    |--------------------------------------------------------------------------
    |
    | Credentials can be found at Parse.com under your app's settings.
    |
    */
    'parse' => [

        /*
        |--------------------------------------------------------------------------
        | Test application credentials
        |--------------------------------------------------------------------------
        */
        'development' => [
            'app_id' => env('PARSE_DEV_APP_ID'),
            'rest_key' => env('PARSE_DEV_REST_KEY'),
            'master_key' => env('PARSE_DEV_MASTER_KEY')
        ],

        /*
        |--------------------------------------------------------------------------
        | Live application credientials
        |--------------------------------------------------------------------------
        */
        'live' => [
            'app_id' => null,
            'rest_key' => null,
            'master_key' => null
        ]
    ]
];