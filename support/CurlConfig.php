<?php
/**
 * 自定义 cURL 配置引导类
 * 符合 webman 引导类规范，用于初始化 cURL 常量和默认选项
 */
namespace support;

class CurlConfig
{
    /**
     * 引导类入口方法（webman 会自动执行该方法）
     */
    public static function start()
    {
        // 定义缺失的 CURL_SSLVERSION_TLSv1_2 常量（全局生效）
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }
    }
}