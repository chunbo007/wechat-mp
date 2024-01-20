<?php

namespace app\wechat\controller;

use app\admin\model\Platform;
use app\common\service\wechat\MiniProgram;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use support\Request;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class OpenApiController
{
    /**
     * 获取token给第三方平台使用
     * @param Request $request
     * @return array
     * @throws BadRequestHttpException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws ModelNotFoundException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getToken(Request $request): array
    {
        // 开放平台应用ID
        $platformAppId = $request->input('platform_appid');
        // 被授权的应用ID
        $appid = $request->input('appid');
        // 校验参数
        if (empty($platformAppId) || empty($appid)) {
            return error('参数错误');
        };

        $platformSetting = Platform::where('app_id', $platformAppId)->find();
        $app = new MiniProgram($platformSetting->id);

        if (empty($platformSetting->third_secret)) {
            return error('请先在wechat-mp开放平台配置外部平台解密secret');
        }

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
        return success($result);
    }
}