<?php

namespace app\wechat\controller;

use app\admin\model\Platform;
use app\common\model\Authorizers;
use app\common\service\Forward;
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
    public function index(Request $request, $appid = '')
    {
        try {
            $xml = $request->rawBody();
            $xml = simplexml_load_string($xml);
            if (empty($xml)) return '请求体为空';

            if ($appid) {
                // 转发消息与事件推送请求给第三方
                Forward::run($request, $appid, 'app');
                // 消息与事件推送包括：设置小程序名称、添加类目、提交代码审核。审核结果会向消息与事件接收 URL 进行事件推送
                $authorizer = Authorizers::with('platform')->where('appid', $appid)->find();
                $platformAppId = $authorizer->platform->app_id;
            } else {
                // 转发授权事件推送请求给第三方
                Forward::run($request, $xml->AppId, 'platform');
                // 授权事件推送包括：验证票据、授权成功、取消授权、授权更新、快速注册企业小程序、快速注册个人小程序、注册试用小程序、试用小程序快速认证、发起小程序管理员人脸核身、申请小程序备案
                $platformAppId = (string)$xml->AppId;
            }
            $platform = Platform::where('app_id', $platformAppId)->find();
            $app = new OpenPlatform($platform['id']);
            return $app->handle($request, $appid);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * 获取授权链接
     * @param Request $request
     * @param $url
     * @return string
     */
    public function authorizer(Request $request, $url = ''): string
    {
        $url = urldecode($url);
        return "<a href='$url' target='_blank'>点击授权</a>";
    }

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
            'platform_id' => $platformAppId,
            'app_id' => $appid,
            'component_access_token' => $component_access_token,
            'authorizer_access_token' => $authorizer_access_token
        ];
        return encrypt(json_encode($result), $platformSetting->third_secret);
    }
}
