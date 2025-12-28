<?php

namespace app\common\service\wechat;


use app\admin\model\Authorizers;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Http\Response;
use EasyWeChat\Kernel\Support\Collection;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;
use app\admin\model\Tester;

class MiniProgram extends OpenPlatform
{
    public function getMiniProgram($appid): \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
    {
        $platform = Authorizers::where('appid', $appid)->find();
        return $this->app->miniProgram($platform['appid'], $platform['refreshtoken']);
    }

    /**
     * 获取当前小程序代码信息
     * @param $appid
     * @return array
     * @throws GuzzleException
     * @throws InvalidConfigException
     * @throws BadRequestHttpException
     */
    public function getVersionDetail($appid): array
    {
        try {
            $result = [];
            $version = $this->getVersionInfo($appid);
            if (!empty($version['release_info'])) {
                $version['release_info']['release_qrcode'] = base64_encode($this->getQrCode($appid));
            }
            if (!empty($version['exp_info'])) {
                $version['exp_info']['exp_qrcode'] = base64_encode($this->getExpQrCode($appid));
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
            return $this->getMiniProgram($appid)->access_token->getToken();
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
        return $this->getMiniProgram($appid)->base->httpPostJson('wxa/getvisitstatus', []);
    }

    /**
     * 获取版本信息
     * @param $appid
     * @return mixed
     */
    public function getVersionInfo($appid)
    {
        return $this->getMiniProgram($appid)->base->httpPostJson('wxa/getversioninfo', []);
    }

    /**
     * 获取小程序码
     * @param $appid
     * @return array|Collection|object|ResponseInterface|string
     */
    public function getQrCode($appid)
    {
        return $this->getMiniProgram($appid)->app_code->getUnlimit('wxcomponent');
    }

    /**
     * 获取体验版二维码
     * @param $appid
     * @return Response
     * @throws InvalidConfigException
     * @throws GuzzleException
     */
    public function getExpQrCode($appid)
    {
        return $this->getMiniProgram($appid)->code->getQrCode();
    }

    /**
     * 获取最后一次审核状态
     * @param $appid
     * @return array|Collection|object|ResponseInterface|string
     * @throws InvalidConfigException
     */
    public function getLatestAuditStatus($appid)
    {
        return $this->getMiniProgram($appid)->code->getLatestAuditStatus();
    }

    /**
     * 上传代码并生成体验版
     * @param $appid
     * @param $data
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws InvalidConfigException
     */
    public function commit($appid, $data)
    {
        $template_id = $data['template_id'];
        $ext_json = $data['ext_json'];
        $user_version = $data['user_version'];
        $user_desc = $data['user_desc'];
        return $this->getMiniProgram($appid)->code->commit($template_id, $ext_json, $user_version, $user_desc);
    }

    public function getCategory($appid)
    {
        return $this->getMiniProgram($appid)->code->getCategory();
    }

    public function submitAudit($appid, $data)
    {
        return $this->getMiniProgram($appid)->code->submitAudit($data);
    }

    public function undoAudit($appid)
    {
        return $this->getMiniProgram($appid)->code->withdrawAudit();
    }

    public function speedupCodeAudit($appid, $auditid)
    {
        return $this->getMiniProgram($appid)->code->speedupAudit((int)$auditid);
    }

    public function release($appid)
    {
        return $this->getMiniProgram($appid)->code->release();
    }

    public function revertCodeRelease($appid)
    {
        return $this->getMiniProgram($appid)->code->rollbackRelease();
    }

    public function setDomain($appid, $params)
    {
        $params['action'] = 'set';
        return $this->getMiniProgram($appid)->domain->modify($params);
    }

    public function getTester($appid)
    {
        $result = $this->getMiniProgram($appid)->tester->list();
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
        $result = $this->getMiniProgram($appid)->tester->bind($wechatId);
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
        return $this->getMiniProgram($appid)->tester->unbind(null,$userStr);
    }

    /**
     * 获取小程序隐私设置
     * @param $appid
     * @param int $privacyVer | 1: 现网版本 2: 开发版本
     * @return mixed
     */
    public function getPrivacy($appid, int $privacyVer = 2)
    {
        return $this->getMiniProgram($appid)->base->httpPostJson('cgi-bin/component/getprivacysetting', [ 'privacy_ver' => $privacyVer ]);
    }

    public function setPrivacy($appid, $privacy)
    {
        $privacy = json_decode($privacy,true);
        return $this->getMiniProgram($appid)->privacy->set($privacy);
    }
}