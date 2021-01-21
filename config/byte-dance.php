<?php

return [
    'timeout' => env('BYTE_DANCE_TIMEOUT', 30.30),

    'block_size' => env('BYTE_DANCE_BLOCK_SIZE', 20971520),
    // 普通号：individual，企业号：enterprise，服务商：service
    'account_type' => env('BYTE_DANCE_ACCOUNT_TYPE', 'individual'),

    'client_key' => env('BYTE_DANCE_CLIENT_KEY', ''),
    'client_secret' => env('BYTE_DANCE_CLIENT_SECRET', ''),

    // 沙箱：dev，上线：prod
    'mode' => env('BYTE_DANCE_MODE', 'prod'),
    'sandbox_client_key' => env('BYTE_DANCE_DEV_CLIENT_KEY', ''),
    'sandbox_client_secret' => env('BYTE_DANCE_DEV_CLIENT_SECRET', ''),
];