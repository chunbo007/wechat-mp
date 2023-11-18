<?php

namespace app\wechat\controller;
use app\common\service\wechat\OpenPlatform;
use EasyWeChat\Factory;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $xml = $request->rawBody();
        $xml = simplexml_load_string($xml);
        if (empty($xml)) return '请求体为空';
        $appId = (string)$xml->AppId;
        $openPlatformConfig = config("wechat.open_platform.$appId");
        $app =  new OpenPlatform($openPlatformConfig);
        return $app->handle($request);
      }
}
