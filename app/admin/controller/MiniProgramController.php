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
        $row = Authorizers::where('id', $id)->find();
        $miniprogram = new MiniProgram($row['platform_id']);
        $row['visit_status'] = $miniprogram->getVisitStatus($row['appid']);
        $row['version'] = $miniprogram->getVersionInfo($row['appid']);
        return success($row);
    }
}