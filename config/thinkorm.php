<?php

return [
    'default' => env('DB_CONNECTION', 'mysql'),
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type' => 'mysql',
            // 服务器地址
            'hostname' => env('DB_HOST'),
            // 数据库名
            'database' => env('DB_DATABASE'),
            // 数据库用户名
            'username' => env('DB_USERNAME'),
            // 数据库密码
            'password' => env('DB_PASSWORD'),
            // 数据库连接端口
            'hostport' => env('DB_PORT'),
            // 数据库连接参数
            'params' => [
                // 连接超时3秒
                \PDO::ATTR_TIMEOUT => 3,
            ],
            // 数据库编码默认采用utf8
            'charset' => 'utf8',
            // 数据库表前缀
            'prefix' => '',
            // 断线重连
            'break_reconnect' => true,
            // 自定义分页类
            'bootstrap' =>  ''
        ],
    ],
];
