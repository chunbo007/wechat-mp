<?php

namespace app\admin\controller;

use app\admin\model\Authorizers;
use app\admin\model\Platform;
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
        try {
            $data = $request->post();
            $platform = Platform::where('id', $data['platform_id'])->find();
            if (!$platform) throw new BadRequestHttpException('平台不存在');
            $authorizer = new Authorizer($platform);
            $authorizer->refresh($data);
            return success();
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
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