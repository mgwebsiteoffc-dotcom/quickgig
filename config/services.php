<?php

return [

    'razorpay' => [
        'key'            => env('RAZORPAY_KEY'),
        'secret'         => env('RAZORPAY_SECRET'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
    ],

    'razorpayx' => [
        'key'            => env('RAZORPAYX_KEY', env('RAZORPAY_KEY')),
        'secret'         => env('RAZORPAYX_SECRET', env('RAZORPAY_SECRET')),
        'account_number' => env('RAZORPAYX_ACCOUNT_NUMBER'),
        'webhook_secret' => env('RAZORPAYX_WEBHOOK_SECRET', env('RAZORPAY_WEBHOOK_SECRET')),
    ],

];
