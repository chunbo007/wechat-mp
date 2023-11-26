<?php

namespace app\admin\controller;

use app\admin\model\Platform;
use support\Request;
use support\Response;
use think\db\exception\DbException;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class PlatformController extends BaseController
{
    /**
     * @throws BadRequestHttpException
     */
    public function add(Request $request)
    {
        $data = $request->post();
        Platform::add($data);
        return success();
    }

    /**
     * @throws BadRequestHttpException
     */
    public function edit(Request $request)
    {
        $data = $request->post();
        Platform::edit($data);
        return success();
    }

    public function delete(Request $request)
    {
        $id = $request->post('id');
        $res = Platform::destroy($id);
        if ($res) {
            return success();
        } else {
            return error();
        }
    }

    /**
     * @param Request $request
     * @return array|Response
     * @throws DbException
     */
    public function list(Request $request)
    {
        return success(Platform::list($request));
    }
}