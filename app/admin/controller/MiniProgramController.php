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
//        $row['visit_status'] = $miniprogram->getVisitStatus($row['appid']);
        $version = $miniprogram->getVersionInfo($row['appid']);
        if (!empty($version['release_info'])) {
            $version['release_info']['release_qrcode'] = base64_encode($miniprogram->getQrCode($row['appid']));
        }
        if (!empty($version['exp_info'])) {
            $version['exp_info']['exp_qrcode'] = base64_encode($miniprogram->getExpQrCode($row['appid']));
        }
//        $json = [
//            'errcode' => 0,
//            'errmsg'  => 'ok',
//            'auditid' => 1234567,
//            'status'  => 4,
//            'reason'  => '账号信息不合规范',
//            'ScreenShot' => 'xx|yy|zz',
//            'user_version' => 'V1.5',
//            'user_desc' => 'xxx',
//            'submit_audit_time' => '1640763673'
//        ];
        $version['audit_info'] = $miniprogram->getLatestAuditStatus($row['appid']);
//        $version['audit_info'] = $json;
        $row['version'] = $version;
        $row['code_template'] = $miniprogram->getTemplate();
        return success($row);
    }
}