<?php

namespace app\admin\controller;

use app\admin\model\Authorizers;
use app\admin\model\Platform;
use app\common\service\wechat\OpenPlatform;
use support\Request;
use support\Response;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

/**
 * 授权账号管理
 */
class AuthorizerController extends BaseController
{
    /**
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws BadRequestHttpException
     */
    public function refresh(Request $request)
    {
        $data = $request->post();
        $platform = Platform::where('id', $data['platform_id'])->find();
        if (!$platform) throw new BadRequestHttpException('平台不存在');
        $authorizer = new OpenPlatform($platform);
        $authorizer->refresh($data);
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