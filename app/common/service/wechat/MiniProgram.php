<?php

namespace app\common\service\wechat;


use app\admin\model\Authorizers;
use Psr\SimpleCache\InvalidArgumentException;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class MiniProgram extends OpenPlatform
{
    /**
     * @throws InvalidArgumentException
     * @throws BadRequestHttpException
     */
    public function getToken($appid)
    {
        try {
            var_dump($appid);
            $platform = Authorizers::where('appid', $appid)->find();
            $MiniProgram = $this->app->miniProgram($platform['appid'], $platform['refreshtoken']);
            return $MiniProgram->access_token->getToken();
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

}