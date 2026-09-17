<?php
return [
    'enable' => env('SQL_LOG', false),
    'exception' => [
        // 是否记录异常到日志
        'enable' => true,
        // 不会记录到日志的异常类
        'dontReport' => [
            support\exception\BusinessException::class
        ]
    ],
    'dontReport' => [
        'app' => [],
        'controller' => [],
        'action' => [],
        'path' => []
    ],
    'channel' => 'sql' // 日志通道(在config/log.php里配置,默认是default)
];
