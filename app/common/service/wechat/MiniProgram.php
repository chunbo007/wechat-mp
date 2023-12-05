<?php

namespace app\common\service\wechat;


use app\admin\model\Authorizers;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class MiniProgram extends OpenPlatform
{
    public function getMiniProgram($appid): \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
    {
        $platform = Authorizers::where('appid', $appid)->find();
        return $this->app->miniProgram($platform['appid'], $platform['refreshtoken']);
    }

    public function getToken($appid): array
    {
        try {
            return $this->getMiniProgram($appid)->access_token->getToken();
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    public function getVisitStatus($appid)
    {
        return $this->getMiniProgram($appid)->base->httpPostJson('wxa/getvisitstatus', []);
    }

    public function getVersionInfo($appid)
    {
        return $this->getMiniProgram($appid)->base->httpPostJson('wxa/getversioninfo', []);
    }

}