<?php

namespace app\common\service\wechat;


use EasyWeChat\Factory;
use EasyWeChat\OfficialAccount\Application;
use GuzzleHttp\Exception\GuzzleException;
use Overtrue\Socialite\Exceptions\AuthorizeFailedException;
use Overtrue\Socialite\User;

class OfficialAccount
{
    static Application $app;

    /**
     * 获取网页授权链接
     * @param $redirect
     * @return string
     */
    static function getOauthUrl($redirect): string
    {
        return self::getApp()->oauth->scopes(['snsapi_base'])->redirect($redirect);
    }

    static function getApp(): Application
    {
        self::$app = Factory::officialAccount([
            'app_id' => env('OFFICIAL_APPID'),         // AppID
            'secret' => env('OFFICIAL_SECRET'),     // AppSecret
        ]);
        return self::$app;
    }

    /**
     * @param $code
     * @return User
     * @throws GuzzleException
     * @throws AuthorizeFailedException
     */
    static function userFromCode($code): User
    {
        return self::getApp()->oauth->userFromCode($code);
    }
}