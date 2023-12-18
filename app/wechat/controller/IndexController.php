<?php

namespace app\wechat\controller;

use app\admin\model\Platform;
use app\common\service\wechat\OpenPlatform;
use support\Request;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class IndexController
{
    /**
     * 处理微信平台消息入口
     * @throws BadRequestHttpException
     */
    public function index(Request $request, $appid = '')
    {
        try {
            $xml = $request->rawBody();
            $xml = simplexml_load_string($xml);
            if (empty($xml)) return '请求体为空';
            $appId = (string)$xml->AppId;
            $platform = Platform::where('app_id', $appId)->find();
            $app = new OpenPlatform($platform['id']);
            return $app->handle($request, $appid);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

    }

    public function authorizer(Request $request, $url = '')
    {
        $url = urldecode($url);
        return "<a href='$url' target='_blank'>点击授权</a>";
    }
}
