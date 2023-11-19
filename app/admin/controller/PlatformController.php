<?php

namespace app\admin\controller;

use app\admin\model\Platform;
use support\Request;
use support\Response;
use think\db\exception\DbException;

class PlatformController extends BaseController
{
    public function add(Request $request)
    {
        $data = $request->post();
        $res = Platform::create($data);
        if ($res->id) {
            return success();
        } else {
            return error('添加失败');
        }
    }

    public function edit(Request $request)
    {
        $data = $request->post();
        $res = Platform::update($data);
        if ($res) {
            return success();
        } else {
            return error('修改失败');
        }
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