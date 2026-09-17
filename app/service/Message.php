<?php
namespace app\service;
use plugin\saiadmin\app\logic\system\SystemConfigLogic;
use plugin\saiadmin\utils\Arr;
use Ymlluo\GroupRobot\GroupRobot;

/**
 * 发送管理消息
 */
Class Message {
    public static function send($markdown): void
    {
        $logic = new SystemConfigLogic();
        $uploadConfig = $logic->getGroup('message_config');
        $channel = Arr::getConfigValue($uploadConfig,'channel');
        $url = Arr::getConfigValue($uploadConfig,'url');
        $sign = Arr::getConfigValue($uploadConfig,'sign');
        if(empty($url) || empty($sign)){
            return;
        }
        $robot = new GroupRobot($channel,$url,$sign);
        $robot->markdown($markdown)->send();
    }
}