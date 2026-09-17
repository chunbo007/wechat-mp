<?php
// 安装期间禁用队列，避免数据库连接错误
$installLock = runtime_path('install.lock');
$envFile = base_path(false) . DIRECTORY_SEPARATOR . '.env';
$installed = file_exists($installLock) && file_exists($envFile);
return [
    'enable' => $installed,
];