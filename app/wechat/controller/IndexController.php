<?php
/**
 * 小程序
 */
namespace app\wechat\controller;

use app\admin\model\Platform;
use app\common\model\Authorizers;
use app\common\service\Forward;
use app\common\service\wechat\OpenPlatform;
use support\Cache;
use support\Log;
use support\Request;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class IndexController extends BaseController
{
    /**
     * 处理微信平台消息入口
     * @throws BadRequestHttpException
     */
    public function index(Request $request, $appid = '')
    {
        try {
            $xml = $request->rawBody();
            // 过滤掉重复请求
            $key = 'wechat_mp_' . md5(json_encode($xml));
            if (Cache::has($key)) {
                Log::info('收到重复请求，已过滤');
                return 'success';
            } else {
                Cache::set($key, '', 10);
            }
            $xml = simplexml_load_string($xml);
            if (empty($xml)) return 'success';
            if ($appid) {
                // 转发消息与事件推送请求给第三方
                $forwardResult = Forward::run($request, $appid, 'app');
                // 消息与事件推送包括：设置小程序名称、添加类目、提交代码审核。审核结果会向消息与事件接收 URL 进行事件推送
                $authorizer = Authorizers::with('platform')->where('appid', $appid)->find();
                $platformAppId = $authorizer->platform->app_id;
            } else {
                // 转发授权事件推送请求给第三方
                $forwardResult = Forward::run($request, $xml->AppId, 'platform');
                // 授权事件推送包括：验证票据、授权成功、取消授权、授权更新、快速注册企业小程序、快速注册个人小程序、注册试用小程序、试用小程序快速认证、发起小程序管理员人脸核身、申请小程序备案
                $platformAppId = (string)$xml->AppId;
            }
            $platform = Platform::where('app_id', $platformAppId)->find();
            $app = new OpenPlatform($platform['id']);
            $result = $app->handle($request, $appid);

            if ($forwardResult !== false) {
                return $forwardResult;
            } else {
                return $result;
            }
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * 获取授权链接
     * @param Request $request
     * @param string $url
     * @return string
     */
    public function authorizer(Request $request, string $url = ''): string
    {
        $url = urldecode($url);
        return "<a href='$url' target='_blank'>点击授权</a>";
    }
}
