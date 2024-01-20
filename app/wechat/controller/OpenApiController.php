<?php

namespace app\wechat\controller;

use app\admin\model\Platform;
use app\common\service\wechat\MiniProgram;
use support\Request;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class OpenApiController
{
    /**
     * 获取token给第三方平台使用
     * @param Request $request
     * @return int
     * @throws BadRequestHttpException
     */
    public function getToken(Request $request)
    {
        // 开放平台应用ID
        $platformAppId = $request->input('platform_appid');
        // 被授权的应用ID
        $appid = $request->input('appid');
        // 校验参数
        if (empty($platformAppId) || empty($appid)) return '参数错误';

        $platformSetting = Platform::where('app_id', $platformAppId)->find();
        $app = new MiniProgram($platformSetting->id);

        if (empty($platformSetting->third_secret)) return '请先在wechat-mp开放平台配置外部平台解密secret';

        // 获取 component_access_token
        $component_access_token = $app->app->access_token->getToken()['component_access_token'];

        // 获取 authorizer_access_token
        $authorizer_access_token = $app->getToken($appid)['authorizer_access_token'];

        $result = [
            'component_appid' => $platformAppId,
            'component_access_token' => $component_access_token,
            'authorizer_appid' => $appid,
            'authorizer_access_token' => $authorizer_access_token
        ];
        return encrypt(json_encode($result), $platformSetting->third_secret);
    }
}