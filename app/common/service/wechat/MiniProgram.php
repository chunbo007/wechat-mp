<?php

namespace app\common\service\wechat;


use app\admin\model\Authorizers;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Http\Response;
use EasyWeChat\Kernel\Support\Collection;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

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
            $version['audit_info'] = $this->getLatestAuditStatus($appid);
            $result['version'] = $version;
            $result['code_template'] = $this->getTemplate();
            /*        $json = [
                        'errcode' => 0,
                        'errmsg'  => 'ok',
                        'auditid' => 1234567,
                        'status'  => 4,
                        'reason'  => '账号信息不合规范',
                        'ScreenShot' => 'xx|yy|zz',
                        'user_version' => 'V1.5',
                        'user_desc' => 'xxx',
                        'submit_audit_time' => '1640763673'
                    ];
                    $version['audit_info'] = $json;*/
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
}