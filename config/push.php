<?php

return [
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
    'provider' => function () {
        return new \Nodes\Push\Providers\UrbanAirshipV3(
            config('nodes.push.urban-airship')
        );
    },

    /*
    |--------------------------------------------------------------------------
    | Urban Airship applications
    |--------------------------------------------------------------------------
    |
    | Settings used by the Urban Airship Push provider
    |
    */
    'urban-airship' => [

        /*
        |--------------------------------------------------------------------------
        | Default platforms
        |
        | Here you can set default platforms for push
        | ios, android, wns
        |--------------------------------------------------------------------------
        */
        'default_platforms' => ['ios', 'android', 'wns'],


        /*
        |--------------------------------------------------------------------------
        | Default app group
        |--------------------------------------------------------------------------
        */
        'default-app-group' => 'default-app-group',

        /*
        |--------------------------------------------------------------------------
        | Proxy
        |--------------------------------------------------------------------------
        | If you need Urban airship called through a proxy, define it here
        |
        | It needs to contain either URL or IP and a port number
        | e.g.
        | 127.0.0.1:8888
        */
        'proxy' => env('URBAN_AIRSHIP_PROXY'),

        /*
        |--------------------------------------------------------------------------
        | App groups
        |--------------------------------------------------------------------------
        | App groups can be used to change between a set of application, can be used
        | if your backend have multiple white label mobile apps, if you only have one
        | just set the default to that
        |
        |
        | In some situations sending to multiple apps at the same time can be handy,
        | Fx sending to several development apps from development/staging
        |
        | Therefore apps are split into "app groups". An app group can contain as
        | many apps as you like. But must all be in an associative array.
        |
        | When sending a push message with Urban Airship, the push provider will
        | take a "app group" and look through each array in that group and send
        | the push message to that app.
        |
        | Empty keys will be ignored in an app will be ignored, no error will be thrown
        |
        | All apps registered within an "app group" must contain three keys:
        | - app_key
        | - app_secret
        | - master_secret
        */
        'app-groups' => [

            /*
            |--------------------------------------------------------------------------
            | Nodes test app
            |--------------------------------------------------------------------------
            */
            'default-app-group' => [
                'app-1' => [
                    'app_key' => env('URBAN_AIRSHIP_APP_KEY'),
                    'app_secret' => env('URBAN_AIRSHIP_APP_SECRET'),
                    'master_secret' => env('URBAN_AIRSHIP_MASTER_SECRET'),
                ],

                // Example of another app in an "app group"
                /*
                'app-2' => [
                    'app_key' => null,
                    'app_secret' => null,
                    'master_secret' => null,
                ]
                */
            ],
        ],
    ],
];
