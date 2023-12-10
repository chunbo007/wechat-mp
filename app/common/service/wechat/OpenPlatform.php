<?php
namespace app\common\service\wechat;

use app\admin\model\Authorizers;
use app\common\service\BaseServices;
use EasyWeChat\Factory;
use EasyWeChat\OpenPlatform\Application;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class OpenPlatform extends BaseServices {
    public Application $app;

    /**
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws BadRequestHttpException
     */
    public function __construct($platform_id)
    {
        $platform = $this->getPlatformParams($platform_id);
        $this->app = Factory::openPlatform([
            'app_id' => $platform['app_id'],
            'secret' => $platform['secret'],
            'token' => $platform['token'],
            'aes_key' => $platform['aes_key'],
            'debug' => true,
            'log' => [
                'default' => 'dev', // 默认使用的 channel，生产环境可以改为下面的 prod
                'channels' => [
                    // 测试环境
                    'dev' => [
                        'driver' => 'single',
                        'path' => runtime_path("logs/wechat-" . date('Y-m-d') . ".log"),
                        'level' => 'debug',
                    ],
                    // 生产环境
                    'prod' => [
                        'driver' => 'daily',
                        'path' => 'D:/php/webman/runtime/logs/workerman.log',
                        'level' => 'info',
                    ],
                ],
            ],
        ]);
    }

    /**
     * 处理开放平台消息
     * @param $request
     * @return false|string
     */
    public function handle($request)
    {
        try {
            $symfony_request = new SymfonyRequest($request->get(), $request->post(), [], $request->cookie(), [], [], $request->rawBody());
            $symfony_request->headers = new HeaderBag($request->header());
            $this->app->rebind('request', $symfony_request);
            $response = $this->app->server->serve();
            return $response->getContent();
        } catch (\Exception $e){
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
            $list = $this->app->getAuthorizers();
            $insert_data = [];
            foreach ($list['list'] as $item) {
                $program = $this->app->getAuthorizer($item['authorizer_appid']);
                $program_authorizer_info = $program['authorizer_info'];
                $program_authorization_info = $program['authorization_info'];
                $insert_data[] = [
                    'platform_id' => $platform_id,
                    'appid' => $item['authorizer_appid'] ?? '',
                    'refreshtoken' => $item['refresh_token'] ?? '',
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
            $model->saveAll($insert_data);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    public function getTemplate()
    {
        return $this->app->code_template->list();
    }
}