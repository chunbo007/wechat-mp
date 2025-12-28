<?php

namespace app\admin\controller;

use app\admin\model\Authorizers;
use app\common\service\wechat\MiniProgram;
use app\common\service\wechat\OpenPlatform;
use support\Request;

class MiniProgramController extends BaseController
{
    public function detail(Request $request)
    {
        $id = $request->post('id');
        $row = Authorizers::where('id', $id)->findOrEmpty()->toArray();
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

    public function getCategory(Request $request)
    {
        $id = $request->post('id');
        $data = $request->post();
        $row = Authorizers::where('id', $id)->find();
        $miniprogram = new MiniProgram($row['platform_id']);
        $result = $miniprogram->getCategory($row['appid'], $data);
        return success($result);
    }

    public function submitAudit(Request $request)
    {
        $id = $request->post('id');
        $data = $request->post();
        $row = Authorizers::where('id', $id)->find();
        $miniprogram = new MiniProgram($row['platform_id']);
        unset($data['id']);
        $result = $miniprogram->submitAudit($row['appid'], $data);
        return json(['code' => $result['errcode'], 'msg' => $result['errmsg']]);
    }

    public function undoAudit(Request $request)
    {
        $id = $request->post('id');
        $row = Authorizers::where('id', $id)->find();
        $miniprogram = new MiniProgram($row['platform_id']);
        $result = $miniprogram->undoAudit($row['appid']);
        return json(['code' => $result['errcode'], 'msg' => $result['errmsg']]);
    }

    public function speedupCodeAudit(Request $request)
    {
        $id = $request->post('id');
        $auditid = $request->post('auditid');
        $row = Authorizers::where('id', $id)->find();
        $miniprogram = new MiniProgram($row['platform_id']);
        $result = $miniprogram->speedupCodeAudit($row['appid'], $auditid);
        return json(['code' => $result['errcode'], 'msg' => $result['errmsg']]);
    }

    public function release(Request $request)
    {
        $id = $request->post('id');
        $row = Authorizers::where('id', $id)->find();
        $miniprogram = new MiniProgram($row['platform_id']);
        $result = $miniprogram->release($row['appid']);
        return json(['code' => $result['errcode'], 'msg' => $result['errmsg']]);
    }

    public function revertCodeRelease(Request $request)
    {
        $id = $request->post('id');
        $row = Authorizers::where('id', $id)->find();
        $miniprogram = new MiniProgram($row['platform_id']);
        $result = $miniprogram->revertCodeRelease($row['appid']);
        return json(['code' => $result['errcode'], 'msg' => $result['errmsg']]);
    }

    public function setDomain(Request $request)
    {
        $request_params = $request->post();
        $id = $request_params['id'];
        unset($request_params['id']);
        $row = Authorizers::where('id', $id)->find();
        $miniprogram = new MiniProgram($row['platform_id']);
        $result = $miniprogram->setDomain($row['appid'], $request_params);
        if ($result['errcode'] == '0') {
            // 如果修改成功，需要重新获取小程序资料
            $authorizer_info = $miniprogram->app->getAuthorizer($row['appid']);
            Authorizers::where('id', $id)->update(['json_data' => json_encode($authorizer_info)]);
        }
        return json(['code' => $result['errcode'], 'msg' => $result['errmsg']]);
    }

    public function getPcAuthorizerUrl(Request $request)
    {
        $id = $request->post('id');
        $open_platform = new OpenPlatform($id);
        $data = $open_platform->getPcAuthorizerUrl();
        return success($data);
    }

    public function getTests(Request $request)
    {
        $id = $request->post('id');
        $row = Authorizers::where('id', $id)->find();
        $miniprogram = new MiniProgram($row['platform_id']);
        $result = $miniprogram->getTester($row['appid']);
        return json(['code' => $result['errcode'], 'msg' => $result['errmsg'], 'data' => $result['members']]);
    }

    public function bindTester(Request $request)
    {
        $id = $request->post('id');
        $wechatId = $request->post('wechat_id');
        $remark = $request->post('remark');
        $row = Authorizers::where('id', $id)->find();
        $miniprogram = new MiniProgram($row['platform_id']);
        $result = $miniprogram->bindTester($row['appid'], $wechatId,$remark);
        return json(['code' => $result['errcode'], 'msg' => $result['errmsg']]);
    }

    public function unbindTester(Request $request)
    {
        $id = $request->post('id');
        $userstr = $request->post('userstr');
        $row = Authorizers::where('id', $id)->find();
        $miniprogram = new MiniProgram($row['platform_id']);
        $result = $miniprogram->unbindTester($row['appid'], $userstr);
        return json(['code' => $result['errcode'], 'msg' => $result['errmsg']]);
    }

    public function getPrivacy(Request $request)
    {
        $id = $request->post('id');
        $privacyVer = $request->post('privacy_ver');
        $row = Authorizers::where('id', $id)->find();
        $miniprogram = new MiniProgram($row['platform_id']);
        $privacy = $miniprogram->getPrivacy($row['appid'],$privacyVer);
        $result['code'] = $privacy['errcode'];
        $result['msg'] = $privacy['errmsg'];
        $result['data'] = $privacy;
        return json($result);
    }

    public function setPrivacy(Request $request)
    {
        $id = $request->post('id');
        $privacy = $request->post('privacy');
        $row = Authorizers::where('id', $id)->find();
        $miniprogram = new MiniProgram($row['platform_id']);
        $result = $miniprogram->setPrivacy($row['appid'],$privacy);
        return json(['code' => $result['errcode'], 'msg' => $result['errmsg']]);
    }

}