<?php

namespace app\admin\controller;

use app\admin\model\Authorizers;
use app\common\service\wechat\MiniProgram;
use support\Request;

class MiniProgramController extends BaseController
{
    public function detail(Request $request)
    {
        $id = $request->post('id');
        $row = Authorizers::where('id', $id)->find()->toArray();
        $miniprogram = new MiniProgram($row['platform_id']);
        $version_detail = $miniprogram->getVersionDetail($row['appid']);
        $row = array_merge($row, $version_detail);
        return success($row);
    }

    public function commit(Request $request)
    {
        $id = $request->post('id');
        $data = $request->post();
        $row = Authorizers::where('id', $id)->find();
        $miniprogram = new MiniProgram($row['platform_id']);
        $result = $miniprogram->commit($row['appid'], $data);
        return success($result);
    }
}