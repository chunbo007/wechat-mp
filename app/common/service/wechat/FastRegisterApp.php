<?php
/**
 * 注册试用小程序事件回调
 */

namespace app\common\service\wechat;

use app\common\model\TrialRecords;
use app\common\service\Yinghuo;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Text;
use support\Log;

class FastRegisterApp
{
    static function callback($message)
    {
        // 开放平台应用appid
        $componentId = $message['AppId'];
        // 小程序appid
        $appid = $message['appid'];
        // 小程序名称
        $storeName = $message['info']['name'];
        // unique_id
        $uniqueId = $message['info']['unique_id'];
        // 保存创建结果
        self::saveResult($uniqueId, $componentId, $appid);
        // 设置domain
        self::setDomain($componentId, $appid);
        // 提交体验版小程序
        self::commit($componentId, $appid);
        // 获取体验码
        $mediaId = self::getExpQrCode($componentId, $appid);
        // 创建账号店铺、生成随机用户名、密码
        $username = generateRandomString(6);
        $password = generateRandomString(8, true);
        self::addStore($storeName, $username, $password);
        // 推送创建结果
        self::pushSuccessMsg($uniqueId, $username, $password);
        // 推送体验码
        self::pushExpQrCode($uniqueId, $mediaId);
    }

    // 保存创建结果
    static function saveResult($uniqueId, $componentId, $appid)
    {
        TrialRecords::update([
            'component_id' => $componentId,
            'appid' => $appid,
        ], ['unique_id' => $uniqueId]);
    }

    // 配置小程序服务器域名
    static function setDomain($componentId, $appid)
    {
        $domainData = '{
            "requestdomain": [
                "https://f.1zh888.com"
            ],
            "wsrequestdomain": [
                "wss://f.1zh888.com"
            ],
            "uploaddomain": [
                "https://f.1zh888.com"
            ],
            "downloaddomain": [
                "https://f.1zh888.com"
            ],
            "udpdomain": [],
            "tcpdomain": []
        }';
        $domainData = json_decode($domainData, true);
        $miniprogram = new MiniProgram($componentId);
        $result = $miniprogram->setDomain($appid, $domainData);
        Log::info('step1 setDomain result', $result);
    }

    // 提交体验版小程序
    static function commit($componentId, $appid)
    {
        $data = '{
          "template_id": 42,
          "ext_json": "{\"extEnable\": true,\"extAppid\": \"wx04fd386bf35103ef\",\"directCommit\": false,\"ext\": {\"storeId\": 10002}\n}",
          "user_version": "1.00",
          "user_desc": "1.00"
        }';
        $data = json_decode($data, true);
        $miniprogram = new MiniProgram($componentId);
        $result = $miniprogram->commit($appid, $data);
        Log::info('step2 commit result', $result);
    }

    // 创建店铺

    static function getExpQrCode($componentId, $appid)
    {
        // 获取小程序体验码
        $miniprogram = new MiniProgram($componentId);
        $stream = $miniprogram->getQrCode($appid);
        $path = public_path("upload" . DIRECTORY_SEPARATOR . "{$appid}.jpg");
        file_put_contents($path, $stream);
        // 上传至素材库
        $uploadResult = OfficialAccount::uploadTempImage($path);
        return $uploadResult['media_id'];
    }

    // 推送消息

    static function addStore($storeName, $username, $password)
    {
        $yinghuo = new Yinghuo();
        $yinghuo->adminLogin(env('YINGHUO_USERNAME'), env('YINGHUO_PASSWORD'));
        $yinghuo->addStore($storeName, $username, $password);
    }

    // 推送小程序体验码

    static function pushSuccessMsg($uniqueId, $username, $password)
    {
        $openId = TrialRecords::where('unique_id', $uniqueId)->value('open_id');
        $text = "恭喜你开店成功！长按识别小程序码，进入你的体验小程序店铺！电脑端登录筋斗云官网，体验更多功能\n" .
            "登录地址：https://f.1zh888.com/store\n" .
            "账号：{$username}\n" .
            "密码：{$password}";
        $message = new Text($text);
        OfficialAccount::sendMessage($openId, $message);
    }

    // 获取小程序体验码并上传至素材库

    static function pushExpQrCode($uniqueId, $mediaId)
    {
        $openId = TrialRecords::where('unique_id', $uniqueId)->value('open_id');
        $message = new Image($mediaId);
        OfficialAccount::sendMessage($openId, $message);
    }
}