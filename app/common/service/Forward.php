<?php

namespace app\common\service;

use app\common\model\Authorizers;
use app\common\model\Platform;
use app\common\model\WxcallbackForward;
use support\Request;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class Forward extends BaseServices
{
    /**
     * 转发请求到第三方
     * @param Request $request
     * @param $appid
     * @param $type
     * @return void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function run(Request $request, $appid, $type)
    {
        $forwardUrl = '';
        if ($type === 'platform') {
            $forwardSetting = self::getPlatformSetting($appid);
            if ($forwardSetting['forward_platform'] && filter_var($forwardSetting['forward_platform'], FILTER_VALIDATE_URL)) {
                $forwardUrl = $forwardSetting['forward_platform'];
            }
        } else {
            $forwardSetting = self::getAppSetting($appid);
            if ($forwardSetting['forward_app']) {
                $forwardUrl = str_replace('$APPID$', $appid, $forwardSetting['forward_app']);
            }
        }

        // 获取GET参数并拼接到转发URL中
        $getParams = http_build_query($request->get());
        $forwardUrl .= '?' . $getParams;
        $xml = $request->rawBody();
        // 将请求转发到另一个地址
        $ch = curl_init($forwardUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        $response = curl_exec($ch);
        curl_close($ch);
        self::writeLog($appid, $forwardUrl, $xml, $response);
    }

    /**
     * 通过开放平台应用id获取转发参数
     * @param $platformAppid
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    private static function getPlatformSetting($platformAppid)
    {
        $authorizer = Platform::where('app_id', $platformAppid)->find();
        return [
            'forward_platform' => $authorizer->forward_platform,
            'forward_app' => $authorizer->forward_app,
        ];
    }

    /**
     * 通过小程序id获取转发参数
     * @param $appid
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    private static function getAppSetting($appid)
    {
        $authorizer = Authorizers::with('platform')->where('appid', $appid)->find();
        return [
            'forward_platform' => $authorizer->platform->forward_platform,
            'forward_app' => $authorizer->platform->forward_app,
        ];
    }

    /**
     * 记录转发日志
     * @return void
     */
    private static function writeLog($appid, $forwardUrl, $xml, $response)
    {
        WxcallbackForward::create([
            'appid' => $appid,
            'url' => $forwardUrl,
            'params' => $xml,
            'response' => $response
        ]);
    }
}