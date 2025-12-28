<?php

namespace app\common\service\wechat;


use app\admin\model\Authorizers;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;
use app\admin\model\Tester;

class MiniProgram extends OpenPlatform
{
    public $miniApp;
    public $api;

    public function getMiniProgram($appid)
    {
        $platform = Authorizers::where('appid', $appid)->find();
        $this->miniApp = $this->app->getMiniAppWithRefreshToken($platform['appid'], $platform['refreshtoken']);
        $this->api = $this->miniApp->getClient();
        return $this->miniApp;
    }

    /**
     * 获取当前小程序代码信息
     * @param $appid
     * @return array
     * @throws BadRequestHttpException
     */
    public function getVersionDetail($appid): array
    {
        try {
            $this->getMiniProgram($appid);
            $result = [];
            $version = $this->getVersionInfo($appid);
            if (!empty($version['release_info'])) {
                $version['release_info']['release_qrcode'] = $this->getQrCode($appid);
            }
            if (!empty($version['exp_info'])) {
                $version['exp_info']['exp_qrcode'] = $this->getExpQrCode($appid);
            }
            $lastAuditResult = $this->getLatestAuditStatus($appid);
            if ($lastAuditResult['errcode'] == 0) {
                $version['audit_info'] = $lastAuditResult;
            }
            $result['version'] = $version;
            $result['code_template'] = $this->getTemplate();
            return $result;
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    public function getToken($appid): array
    {
        try {
            $this->getMiniProgram($appid);
            return ['authorizer_access_token' => $this->miniApp->getAccessToken()->getToken()];
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * 获取小程序状态
     * @param $appid
     * @return mixed
     */
    public function getVisitStatus($appid)
    {
        $this->getMiniProgram($appid);
        return $this->api->postJson('/wxa/getvisitstatus', [])->toArray();
    }

    /**
     * 获取版本信息
     * @param $appid
     * @return mixed
     */
    public function getVersionInfo($appid)
    {
        return $this->api->postJson('/wxa/getversioninfo', [])->toArray();
    }

    /**
     * 获取小程序码
     * @param $appid
     * @return string
     */
    public function getQrCode($appid)
    {
        return $this->api->postJson('/wxa/getwxacodeunlimit', ['scene' => 'wxcomponent'])->toDataUrl();
    }

    /**
     * 获取体验版二维码
     * @param $appid
     * @return string
     */
    public function getExpQrCode($appid)
    {
        return $this->api->get('/wxa/get_qrcode', [])->toDataUrl();
    }

    /**
     * 获取最后一次审核状态
     * @param $appid
     * @return array
     */
    public function getLatestAuditStatus($appid)
    {
        return $this->api->get('/wxa/get_latest_auditstatus', [])->toArray();
    }

    /**
     * 上传代码并生成体验版
     * @param $appid
     * @param $data
     * @return array
     */
    public function commit($appid, $data)
    {
        $this->getMiniProgram($appid);
        $template_id = $data['template_id'];
        $ext_json = $data['ext_json'];
        $user_version = $data['user_version'];
        $user_desc = $data['user_desc'];
        return $this->api->postJson('/wxa/commit', [
            'template_id' => $template_id,
            'ext_json' => $ext_json,
            'user_version' => $user_version,
            'user_desc' => $user_desc
        ])->toArray();
    }

    public function getCategory($appid)
    {
        $this->getMiniProgram($appid);
        return $this->api->get('/wxa/get_category')->toArray();
    }

    public function submitAudit($appid, $data)
    {
        $this->getMiniProgram($appid);
        return $this->api->postJson('/wxa/submit_audit', $data)->toArray();
    }

    public function undoAudit($appid)
    {
        $this->getMiniProgram($appid);
        return $this->api->get('/wxa/undocodeaudit')->toArray();
    }

    public function speedupCodeAudit($appid, $auditid)
    {
        $this->getMiniProgram($appid);
        return $this->api->postJson('/wxa/speedupaudit', ['auditid' => (int)$auditid])->toArray();
    }

    public function release($appid)
    {
        $this->getMiniProgram($appid);
        return $this->api->postJson('/wxa/release')->toArray();
    }

    public function revertCodeRelease($appid)
    {
        $this->getMiniProgram($appid);
        return $this->api->get('/wxa/revertcoderelease')->toArray();
    }

    public function setDomain($appid, $params)
    {
        $this->getMiniProgram($appid);
        $params['action'] = 'set';
        return $this->api->postJson('/wxa/modify_domain', $params)->toArray();
    }

    public function getTester($appid)
    {
        $this->getMiniProgram($appid);
        $result = $this->api->postJson('/wxa/memberauth', ['action' => 'get_experiencer'])->toArray();
        if ($result['errcode'] == 0) {
            $dbTester = Tester::where('authorizer_appid', $appid)->select()->toArray();
            $txTesterUserstrs = array_column($result['members'], 'userstr');
            $dbTesterUserstrs = array_column($dbTester, 'userstr');
            // 小程序后台有添加体验者，系统中未记录，需要wechat-mp系统中也自动添加
            $notInTxArray = array_diff($txTesterUserstrs, $dbTesterUserstrs);
            $insertData = [];
            foreach ($notInTxArray as $userstr) {
                $data = [
                    'authorizer_appid' => $appid,
                    'userstr'  => $userstr,
                    'remark'   => '未知',
                    'wechat_id' => '未知',
                    'create_time' => time(),
                    'update_time' => time(),
                ];
                array_push($insertData, $data);
            }
            if (!empty($insertData)) {
                Tester::insertAll($insertData,false);
            }

            // wechat-mp系统有添加体验者，小程序后台中未记录(可能在平台或其他位置被删了)，需要系统中自动删掉
            $notInDbArray = array_diff($dbTesterUserstrs, $txTesterUserstrs);
            Tester::where('authorizer_appid', $appid)->whereIn('userstr', $notInDbArray)->delete();
            $dbTester = Tester::list(['authorizer_appid' => $appid]);
            return ['errcode' => 0, 'errmsg' => '', 'members' => $dbTester];
        }
        return $result;
    }

    public function bindTester($appid, $wechatId, $remark)
    {
        $this->getMiniProgram($appid);
        $result = $this->api->postJson('/wxa/bind_tester', ['wechatid' => $wechatId])->toArray();
        if ($result['errcode'] == 0) {
            $data = [
                'authorizer_appid' => $appid,
                'wechat_id' => $wechatId,
                'remark'   => $remark,
                'userstr'  => $result['userstr']
            ];
            Tester::create($data);
        }
        return $result;
    }

    public function unbindTester($appid, $userStr)
    {
        $this->getMiniProgram($appid);
        return $this->api->postJson('/wxa/unbind_tester', ['userstr' => $userStr])->toArray();
    }

    /**
     * 获取小程序隐私设置
     * @param $appid
     * @param int $privacyVer | 1: 现网版本 2: 开发版本
     * @return mixed
     */
    public function getPrivacy($appid, int $privacyVer = 2)
    {
        $this->getMiniProgram($appid);
        return $this->api->postJson('/cgi-bin/component/getprivacysetting', ['privacy_ver' => $privacyVer])->toArray();
    }

    public function setPrivacy($appid, $privacy)
    {
        $this->getMiniProgram($appid);
        $privacy = json_decode($privacy, true);
        return $this->api->postJson('/cgi-bin/component/setprivacysetting', $privacy)->toArray();
    }
}