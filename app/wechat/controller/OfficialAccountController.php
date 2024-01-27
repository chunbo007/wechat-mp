<?php
/**
 * 公众号
 */

namespace app\wechat\controller;

use app\common\model\TrialRecords;
use app\common\service\wechat\OfficialAccount;
use app\common\service\wechat\OpenPlatform;
use GuzzleHttp\Exception\GuzzleException;
use Overtrue\Socialite\Exceptions\AuthorizeFailedException;
use support\Request;
use support\Response;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class OfficialAccountController extends BaseController
{
    /**
     * 发起公众号网页授权登录
     * @param Request $request
     * @return Response
     */
    public function oauth(Request $request): Response
    {
        $redirect = env('SITE_URL') . route('wechat.oauthCallback');
        $state = 'wx3a67b967164b59d1';
        $url = OfficialAccount::getOauthUrl($redirect, $state);
        return redirect($url);
    }

    /**
     * 授权成功重定向至申请试用小程序页面
     * @param Request $request
     * @return string|void
     * @throws BadRequestHttpException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function oauthCallback(Request $request)
    {
        $code = $request->get('code');
        $component_appid = $request->get('state');
        if (empty($code)) {
            return '授权失败：无效的code';
        }
        if (empty($component_appid)) {
            return '授权失败：无效的component_appid';
        }

        try {
            $userInfo = OfficialAccount::userFromCode($code);
        } catch (GuzzleException|AuthorizeFailedException $e) {
            return '获取用户信息失败，' . $e->getMessage();
        }
        // 虚拟账号时不进行登录
        if (isset($userInfo['raw']['is_snapshotuser'])) {
            return '请关注公众号后再点击菜单进行操作';
        }

        // 获取创建试用小程序链接
        $id = Db::connect('yinghuo')->table('yoshop_store')->value('max(store_id)') + 1;
        $name = "筋斗云" . $id;
        $openid = $userInfo->getId();
        $openPlatform = new OpenPlatform($component_appid);
        $fastRegister = $openPlatform->fastRegisterBetaApp($name, $openid);
        if ($fastRegister['errcode'] == 0) {
            // 将openid和unique_id存到申请小程序流水表
            TrialRecords::create([
                'open_id' => $openid,
                'unique_id' => $fastRegister['unique_id'],
            ]);
            return redirect($fastRegister['authorize_url']);
        } else {
            return $fastRegister['errmsg'];
        }
    }
}