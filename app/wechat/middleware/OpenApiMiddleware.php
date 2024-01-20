<?php

namespace app\wechat\middleware;

use app\admin\model\Platform;
use Webman\MiddlewareInterface;

class OpenApiMiddleware implements MiddlewareInterface
{
    public function process($request, $handler): \Webman\Http\Response
    {
        // 开放平台应用ID
        $platformAppId = $request->get('platform_appid');
        $sign = $request->get('sign');
        $time = $request->get('time');

        // 校验参数
        if (empty($platformAppId) || empty($sign) || empty($time)) {
            return error('参数不完整，请检查 platform_appid, time, sign 参数是否齐全');
        }

        // 验证签名有效期
        if ($time < time() - 3000 || $time > time() + 3000) {
            return error('签名已过期');
        }

        $platformSetting = Platform::where('app_id', $platformAppId)->find();
        if (empty($platformSetting->third_secret)) {
            return error('请先在开放平台处配置外部平台解密secret');
        }

        // 验证签名
        $data = $request->get();
        unset($data['sign']);
        $secret = $platformSetting->third_secret;
        if (!verifySign($data, $secret, $sign)) {
            return error('签名验证失败');
        }

        return $handler($request);
    }
}