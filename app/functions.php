<?php
/**
 * Here is your custom functions.
 */

use support\exception\BusinessException;
use Webman\Push\Api;
use plugin\saiadmin\app\logic\system\SystemConfigLogic;
use plugin\saiadmin\utils\Arr;

// 将url转换为本地路径
function parseUrl($url): string
{
    $parsed = parse_url($url);
    if (empty($parsed['path'])) {
        return '';
    }

    $logic = new SystemConfigLogic();
    $uploadConfig = $logic->getGroup('upload_config');
    $local_root = Arr::getConfigValue($uploadConfig,'local_root');
    $local_uri = Arr::getConfigValue($uploadConfig,'local_uri');

    // 将路径中的\替换为/
    $str = str_replace('\\', '/', $parsed['path']);
    // 将路径中的访问路径换成储存路径
    $str = str_replace($local_uri, $local_root, $str);
    return realpath(path_combine(base_path(), $str));
}

// 判断channel是否在线
function is_channel_online($channel): bool
{
    $pusher = new Api(str_replace('0.0.0.0', '127.0.0.1', config('plugin.webman.push.app.api')), config('plugin.webman.push.app.app_key'), config('plugin.webman.push.app.app_secret'));
    $channels = $pusher->getChannels();
    return isset($channels['result']['channels'][$channel]);
}

/**
 * 生成签名
 * @param string $componentAppid
 * @param string $componentSecret
 * @param int $timestamp 时间戳
 * @param array $body 请求参数
 * @return string
 */
function gen_sign(string $componentAppid, string $componentSecret, int $timestamp, array $body): string
{
    $bodyMd5 = md5(gen_json_body($body));
    return md5("$componentAppid,$bodyMd5,$timestamp,$componentSecret");
}

/**
 * 生成Json格式的body
 * @param array $body
 * @return string
 */
function gen_json_body(array $body): string
{
    return (count($body) > 0) ? json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '{}';
}

/**
 * 检测是否是演示账号
 */
function filter_tester($userId = ''): void
{
    // 如果是队列调用，则暂时不检测
    if(!request()) return;
    
    // 如果是API调用，则暂时不检测
    if(request()->controller == 'plugin\wechat\app\controller\OpenApiController'){
        return;
    }

    // 如果是微信开放平台调用，则暂时不检测
    if(request()->controller == 'plugin\wechat\app\controller\IndexController'){
        return;
    }

    if (!$userId){
        $result = getCurrentInfo();
        if (!$result) {
            throw new BusinessException('用户信息读取失败,请重新登录', 401);
        }
        $userId = $result['id'];
    }
    // 判断是否是演示账号
    if ($userId == env('DEMO_ID')){
        throw new BusinessException('当前为演示账号，不能执行操作命令',201);
    }
}
