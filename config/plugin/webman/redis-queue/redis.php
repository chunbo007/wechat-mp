<?php
return [
    'default' => [
        'host' => 'redis://'.env('REDIS_HOST').':'.env('REDIS_PORT'),
        'options' => [
            'auth' => env('REDIS_PASSWORD'),       // 密码，字符串类型，可选参数
            'db' => env('REDIS_DATABASE'),            // 数据库
            'prefix' => '',       // key 前缀
            'max_attempts'  => 5, // 消费失败后，重试次数
            'retry_seconds' => 5, // 重试间隔，单位秒
        ]
    ],
];
