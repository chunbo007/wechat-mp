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
        // 新增授权信息
        self::addAuthorizerInfo($componentId, $appid);
        // unique_id
        $uniqueId = $message['info']['unique_id'];
        // 获取申请流水信息
        $trialRecords = self::getTrialRecords($uniqueId);
        // 推送正在创建小程序提示信息
        self::pushWaitInfo($trialRecords['open_id']);
        // 小程序名称
        $storeName = "筋斗云{$trialRecords['store_id']}";
        // 保存创建结果
        self::saveResult($uniqueId, $componentId, $appid);
        // 设置domain
        self::setDomain($componentId, $appid);
        // 提交体验版小程序
        self::commit($componentId, $appid, $trialRecords['store_id']);
        // 获取体验码
        $mediaId = self::getExpQrCode($componentId, $appid);
        // 创建账号店铺、生成随机用户名、密码
        $username = generateRandomString(6, false, true);
        $password = generateRandomString(8, true);
        self::addStore($trialRecords['store_id'], $appid, $storeName, $username, $password);
        // 推送创建结果
        self::pushSuccessMsg($trialRecords['open_id'], $username, $password);
        // 推送体验码
        self::pushExpQrCode($trialRecords['open_id'], $mediaId);
        // 推送管理员注册信息
        self::pushAdminMsg($storeName);
    }

    // 新增授权信息
    static function addAuthorizerInfo($componentId, $appid)
    {
        $openPlatform = new OpenPlatform($componentId);
        $openPlatform->addAuthorizerInfo($appid);
    }

    // 保存创建结果
    static function saveResult($uniqueId, $componentId, $appid)
    {
        TrialRecords::update([
            'component_appid' => $componentId,
            'appid' => $appid,
        ], ['unique_id' => $uniqueId]);
    }

    // 获取申请流水信息
    static function getTrialRecords($uniqueId)
    {
        return TrialRecords::where('unique_id', $uniqueId)->find();
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
    static function commit($componentId, $appid, $storeId, $templateId = 42, $userVersion = '1.0', $userDesc = '小程序初始化')
    {
        $data = '{
          "template_id": ' . $templateId . ',
          "ext_json": "{\"extEnable\": true,\"extAppid\": \"' . $appid . '\",\"directCommit\": false,\"ext\": {\"storeId\": ' . $storeId . '}\n}",
          "user_version": "' . $userVersion . '",
          "user_desc": "' . $userDesc . '"
        }';
        $data = json_decode($data, true);
        $miniprogram = new MiniProgram($componentId);
        $result = $miniprogram->commit($appid, $data);
        Log::info('step2 commit result', $result);
    }

    // 获取小程序体验码并上传至素材库
    static function getExpQrCode($componentId, $appid)
    {
        $miniprogram = new MiniProgram($componentId);
        $stream = $miniprogram->getExpQrCode($appid);
        $path = public_path("upload" . DIRECTORY_SEPARATOR . "{$appid}.jpg");
        file_put_contents($path, $stream);
        // 上传至素材库
        $uploadResult = OfficialAccount::uploadTempImage($path);
        return $uploadResult['media_id'];
    }

    // 创建店铺
    static function addStore($storeId, $appid, $storeName, $username, $password)
    {
        $yinghuo = new Yinghuo();
        // 登录运维平台
        $yinghuo->adminLogin(env('YINGHUO_USERNAME'), env('YINGHUO_PASSWORD'));
        // 添加商店
        $yinghuo->addStore($storeName, $username, $password);
        // 设置功能模块
        $yinghuo->setModule($storeId);
        // 登录商城
        $yinghuo->superLogin($storeId);
        // 设置默认secret
        $yinghuo->settingWxAppInfo($appid);
        // 设置商城默认上传文件设置
        $yinghuo->settingUpload();
        // 设置商城默认注册设置
        $yinghuo->settingRegister();
    }

    // 推送正在创建小程序提示信息
    static function pushWaitInfo($openId)
    {
        $message = new Text('试用小程序创建中，预计需要10秒，请稍候');
        OfficialAccount::sendMessage($openId, $message);
    }

    // 推送小程序体验码
    static function pushSuccessMsg($openId, $username, $password)
    {
        $text = "恭喜你开店成功！长按识别小程序码，进入你的体验小程序店铺！电脑端登录筋斗云官网，体验更多功能\n" .
            "登录地址：https://f.1zh888.com/store\n" .
            "账号：{$username}\n" .
            "密码：{$password}";
        $message = new Text($text);
        OfficialAccount::sendMessage($openId, $message);
    }

    // 推送小程序码
    static function pushExpQrCode($openId, $mediaId)
    {
        $message = new Image($mediaId);
        OfficialAccount::sendMessage($openId, $message);
    }

    // 推送管理员注册信息
    static function pushAdminMsg($storeName)
    {
        OfficialAccount::sendNewRegisterTemplate($storeName);
    }
}