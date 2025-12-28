<?php
namespace app\common\service\wechat;

use app\admin\model\Authorizers;
use app\common\model\WxcallbackBiz;
use app\common\model\WxcallbackComponent;
use app\common\service\BaseServices;
use EasyWeChat\OpenPlatform\Application;
use support\Log;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class OpenPlatform extends BaseServices {
    public Application $app;
    private $platform_id;
    public $api;

    /**
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws BadRequestHttpException
     */
    public function __construct($platform_id)
    {
        $this->platform_id = $platform_id;
        $platform = $this->getPlatformParams($platform_id);
        $this->app = new Application([
            'app_id' => $platform['app_id'],
            'secret' => $platform['secret'],
            'token' => $platform['token'],
            'aes_key' => $platform['aes_key'],
        ]);
        $this->api = $this->app->getClient();
    }

    /**
     * 处理开放平台消息
     * @param $request
     * @param string $appid
     * @return false|string
     */
    public function handle($request, string $appid = '')
    {
        // https://zhuanlan.zhihu.com/p/659727799 授权事件、消息与事件通知
        try {
            $symfony_request = new SymfonyRequest($request->get(), $request->post(), [], $request->cookie(), [], [], $request->rawBody());
            $symfony_request->headers = new HeaderBag($request->header());
            $this->app->setRequestFromSymfonyRequest($symfony_request);
            
            $server = $this->app->getServer();
            $response = $server->serve();
            // 获取消息解密后的内容
            $message = $server->getDecryptedMessage();
            unset($message['Encrypt']);

            // 开放平台消息
            if (isset($message['InfoType'])) {
                // 授权事件 日志记录
                $this->addComponentCallBackRecord($message);
                switch ($message['InfoType']) {
                    case 'authorized':
                    case 'updateauthorized':
                        $this->addAuthorizerInfo($message);
                        break;
                    case 'unauthorized':
                        $this->delAuthorizerInfo($message);
                        break;
                    default:
                        break;
                }
            }
            // 应用消息
            else if (isset($message['Event'])) {
                // 消息与事件通知 日志记录
                $this->addWxcallbackBizRecord($message, $appid);
            }

            return $response->getBody()->getContents();
        } catch (\Exception $e){
            Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * 重新获取授权账号列表
     * @param $platform_id
     * @throws BadRequestHttpException
     */
    public function refresh($platform_id)
    {
        try {
            Db::startTrans();
            $model = new Authorizers();
            $model->where('platform_id', $platform_id)->delete();
            $list = $this->api->postJson('/cgi-bin/component/api_get_authorizer_list', [
                'component_appid' => $this->app->getAccount()->getAppId(),
                'offset' => 0,
                'count' => 500
            ])->toArray();
            $insert_data = [];
            if (isset($list['list'])) {
                foreach ($list['list'] as $item) {
                    $program = $this->getAuthorizerInfo($item['authorizer_appid']);
                    $program_authorizer_info = $program['authorizer_info'];
                    $program_authorization_info = $program['authorization_info'];
                $insert_data[] = [
                    'platform_id' => $platform_id,
                    'appid' => $item['authorizer_appid'] ?? '',
                    'refreshtoken' => $program_authorization_info['authorizer_refresh_token'],
                    'auth_time' => $item['auth_time'] ?? '',
                    'app_type' => isset($program_authorizer_info['MiniProgramInfo']),
                    'nick_name' => $program_authorizer_info['nick_name'] ?? '',
                    'user_name' => $program_authorizer_info['user_name'] ?? '',
                    'head_img' => $program_authorizer_info['head_img'] ?? '',
                    'qrcode_url' => $program_authorizer_info['qrcode_url'] ?? '',
                    'principal_name' => $program_authorizer_info['principal_name'] ?? '',
                    'register_type' => $program_authorizer_info['register_type'] ?? '',
                    'verify_info' => $program_authorizer_info['verify_type_info']['id'] ?? '',
                    'service_type' => $program_authorizer_info['service_type_info']['id'] ?? '',
                    'account_status' => $program_authorizer_info['account_status'] ?? '',
                    'is_phone' => $program_authorizer_info['basic_config']['is_phone_configured'] ?? '',
                    'is_email' => $program_authorizer_info['basic_config']['is_email_configured'] ?? '',
                    'func_info' => $program_authorization_info['func_info'] ? json_encode($program_authorization_info['func_info'], JSON_UNESCAPED_UNICODE) : '',
                    'json_data' => json_encode($program, true),
                ];
            }
            }
            $model->saveAll($insert_data);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * 保存小程序信息
     * */
    public function addAuthorizerInfo($data)
    {
        $program = $this->getAuthorizerInfo($data['AuthorizerAppid']);
        $program_authorizer_info = $program['authorizer_info'];
        $program_authorization_info = $program['authorization_info'];
        // 有时候平台不返回refresh_token，需要根据code手动去获取refresh_token
        // 刷新令牌authorizer_refresh_token是需要一直保存的？有有效期这个说法吗？
        // https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/troubleshooting/TroubleShooting.html
        if (!$program_authorization_info['authorizer_refresh_token'] && isset($data['AuthorizationCode'])){
            $program_authorization_info = $this->api->postJson('/cgi-bin/component/api_query_auth', [
                'component_appid' => $this->app->getAccount()->getAppId(),
                'authorization_code' => $data['AuthorizationCode']
            ])->toArray();
            $program_authorization_info = $program_authorization_info['authorization_info'];
        }
        $insert_data = [
            'platform_id' => $this->platform_id,
            'appid' => $program_authorization_info['authorizer_appid'] ?? '',
            'refreshtoken' => $program_authorization_info['authorizer_refresh_token'] ?? '',
            'auth_time' => time(),
            'app_type' => isset($program_authorizer_info['MiniProgramInfo']),
            'nick_name' => $program_authorizer_info['nick_name'] ?? '',
            'user_name' => $program_authorizer_info['user_name'] ?? '',
            'head_img' => $program_authorizer_info['head_img'] ?? '',
            'qrcode_url' => $program_authorizer_info['qrcode_url'] ?? '',
            'principal_name' => $program_authorizer_info['principal_name'] ?? '',
            'register_type' => $program_authorizer_info['register_type'] ?? '',
            'verify_info' => $program_authorizer_info['verify_type_info']['id'] ?? '',
            'service_type' => $program_authorizer_info['service_type_info']['id'] ?? '',
            'account_status' => $program_authorizer_info['account_status'] ?? '',
            'is_phone' => $program_authorizer_info['basic_config']['is_phone_configured'] ?? '',
            'is_email' => $program_authorizer_info['basic_config']['is_email_configured'] ?? '',
            'func_info' => $program_authorization_info['func_info'] ? json_encode($program_authorization_info['func_info'], JSON_UNESCAPED_UNICODE) : '',
            'json_data' => json_encode($program, true),
        ];
        $row = Authorizers::find(['appid' => $insert_data['appid']]);
        if ($row){
            Authorizers::update($insert_data, ['appid' => $insert_data['appid']]);
        }else{
            Authorizers::create($insert_data);
        }
    }

    /**
     * 删除小程序授权信息
     * @return void
     */
    public function delAuthorizerInfo($data){
        Authorizers::destroy(['appid' => $data['AuthorizerAppid']]);
    }

    public function getTemplate()
    {
        return $this->api->get('/wxa/gettemplatelist')->toArray();
    }

    private function addComponentCallBackRecord($data)
    {
        $row = [
            'appid' => $data['AppId'],
            'authorizer_appid' => $data['AuthorizerAppid'] ?? null,
            'infotype' => $data['InfoType'],
            'postbody' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'receivetime' => $data['CreateTime']
        ];
        WxcallbackComponent::create($row);
    }

    private function addWxcallbackBizRecord($data, $appid)
    {
        $row = [
            'appid' => $appid,
            'tousername' => $data['ToUserName'],
            'msgtype' => $data['MsgType'],
            'event' => $data['Event'],
            'postbody' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'receivetime' => $data['CreateTime']
        ];
        WxcallbackBiz::create($row);
    }

    public function getPcAuthorizerUrl()
    {
        $callback = env('SITE_URL') . '/auth/callback';
        $pc_url = $this->app->createPreAuthorizationUrl($callback);
        $mobile_url = 'https://open.weixin.qq.com/wxaopen/safe/bindcomponent?action=bindcomponent&no_scan=1&component_appid='.$this->app->getAccount()->getAppId().'&pre_auth_code='.($this->app->createPreAuthorizationCode()['pre_auth_code']).'&redirect_uri='.$callback.'&auth_type=3#wechat_redirect';
        return [
            'pc_url' => urlencode($pc_url),
            'mobile_url' => urlencode($mobile_url)
        ];
    }

    /**
     * 获取授权方信息
     * @param $authorizerAppid
     * @return array
     */
    public function getAuthorizerInfo($authorizerAppid)
    {
        $result = $this->api->postJson('/cgi-bin/component/api_get_authorizer_info', [
            'component_appid' => $this->app->getAccount()->getAppId(),
            'authorizer_appid' => $authorizerAppid
        ])->toArray();
        if (isset($result['errcode']) && $result['errcode'] !== 0) {
            throw new BadRequestHttpException('获取授权方信息失败: '.$result['errcode'].' '.$result['errmsg']);
        }
        return $result;
    }
}