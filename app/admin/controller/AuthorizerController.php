<?php

namespace app\admin\controller;

use app\admin\model\Authorizers;
use app\common\service\wechat\Authorizer;
use support\Request;
use support\Response;
use think\db\exception\DbException;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

/**
 * 授权账号管理
 */
class AuthorizerController extends BaseController
{
    /**
     * @throws BadRequestHttpException
     */
    public function refresh(Request $request)
    {
        $appId = 'wx3a67b967164b59d1';
        $openPlatformConfig = config("wechat.open_platform.$appId");
        $authorizer = new Authorizer($openPlatformConfig);
        $authorizer->refresh($request);
        return success();
    }

    /**
     * @param Request $request
     * @return array|Response
     * @throws DbException
     */
    public function list(Request $request)
    {
        return success(Authorizers::list($request));
    }
}