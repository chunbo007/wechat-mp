<?php

namespace app\bootstrap;

use Webman\Bootstrap;

/**
 * InitSystem 初始化系统环境
 */
class InitSystem implements Bootstrap
{
    public static function start($worker)
    {

        // 定义 CURL_SSLVERSION_TLSv1_2 常量，解决未定义问题
        if (!\defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6); // 6 是 TLS 1.2 对应的标准值
        }

        // Is it console environment ?
        $is_console = !$worker;
        if ($is_console) {
            // If you do not want to execute this in console, just return.
            return;
        }


    }

}
