<?php

return [
    'enable' => true,
    // 错误异常配置
    'exception_handler' => [
        // 不需要记录错误日志
        'dont_report' => [
            Tinywan\ExceptionHandler\Exception\BadRequestHttpException::class,
            Tinywan\ExceptionHandler\Exception\UnauthorizedHttpException::class,
            Tinywan\ExceptionHandler\Exception\ForbiddenHttpException::class,
            Tinywan\ExceptionHandler\Exception\NotFoundHttpException::class,
            Tinywan\ExceptionHandler\Exception\RouteNotFoundException::class,
            Tinywan\ExceptionHandler\Exception\TooManyRequestsHttpException::class,
            Tinywan\ExceptionHandler\Exception\ServerErrorHttpException::class,
//            Tinywan\Validate\Exception\ValidateException::class,
            Tinywan\Jwt\Exception\JwtTokenException::class,
            Tinywan\Jwt\Exception\JwtTokenExpiredException::class
        ],
        // 自定义HTTP状态码
        'status' => [
            'validate' => 400, // 验证器异常
            'jwt_token' => 401, // 认证失败
            'jwt_token_expired' => 401, // 访问令牌过期
            'jwt_refresh_token_expired' => 402, // 刷新令牌过期
            'server_error' => 500, // 服务器内部错误
        ],
        // 自定义响应消息
        'body' => [
            'code' => 0,
            'msg' => '服务器内部异常',
            'data' => null
        ],
        // 事件，event 与 webman/event 存在冲突，event 重命名为 event_trigger
        'event_trigger' => [
            'enable' => false,
            // 钉钉机器人
            'dingtalk' => [
                'accessToken' => '85b834cadc3905ee57ff6807f3b65eee1cac7a1f29296635d81c24f9850aa5ad',
                'secret' => 'SEC0309ed80d09e43c772bc336e72df0a6c27b13d79afd71bb0e91341e866261b9b',
                'title' => '钉钉机器人异常通知',
            ]
        ],
    ],

];