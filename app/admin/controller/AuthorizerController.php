<?php

namespace app\admin\controller;

use app\admin\model\Authorizers;
use app\common\service\wechat\MiniProgram;
use app\common\service\wechat\OpenPlatform;
use Psr\SimpleCache\InvalidArgumentException;
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
     * @param Request $request
     * @return array|Response
     * @throws BadRequestHttpException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function refresh(Request $request)
    {
        $data = $request->post();
        $authorizer = new OpenPlatform($data['platform_id']);
        $authorizer->refresh($data['platform_id']);
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

    /**
     * @param Request $request
     * @return array|Response
     * @throws BadRequestHttpException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws InvalidArgumentException
     * @throws ModelNotFoundException
     */
    public function getToken(Request $request)
    {
        $data = $request->post();
        $MiniProgram = new MiniProgram($data['platform_id']);
        $token = $MiniProgram->getToken($data['appid']);
        return success($token);
    }

    public function getRefreshToken(Request $request)
    {
        $data = $request->post();
        $row = Authorizers::where('appid', $data['appid'])->find();
        return success($row['refreshtoken']);
    }

    public function originalMessage(Request $request)
    {
        $data = $request->post();
        $row = Authorizers::where('appid', $data['appid'])->find();
        return success($row['json_data']);
    }
}