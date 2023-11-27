<?php

namespace app\wechat\controller;

use app\admin\model\Platform;
use app\common\service\wechat\MiniProgram;
use app\common\service\wechat\OpenPlatform;
use support\Request;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class IndexController
{
    /**
     * 处理微信平台消息入口
     * @throws BadRequestHttpException
     */
    public function index(Request $request)
    {
        try {
            $xml = $request->rawBody();
            $xml = simplexml_load_string($xml);
            if (empty($xml)) return '请求体为空';
            $appId = (string)$xml->AppId;
            $openPlatformConfig = Platform::where('app_id', $appId)->find();
            $app = new OpenPlatform($openPlatformConfig);
            return $app->handle($request);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

    }

    public function test()
    {
        $appId = 'wx3a67b967164b59d1';
        $openPlatformConfig = Platform::where('app_id', $appId)->find();
        $app = new MiniProgram($openPlatformConfig);
        return success($app->test());
    }
}
