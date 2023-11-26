<?php

namespace app\common\service\wechat;

use app\admin\model\Authorizers;
use app\common\service\BaseServices;
use EasyWeChat\Factory;
use EasyWeChat\OpenPlatform\Application;
use think\facade\Db;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class Authorizer extends BaseServices
{
    public Application $app;

    public function __construct($config)
    {
        $this->app = Factory::openPlatform([
            'app_id' => $config['app_id'],
            'secret' => $config['secret'],
            'token' => $config['token'],
            'aes_key' => $config['aes_key'],
        ]);
    }

    /**
     * 重新获取授权账号列表
     * @param $data
     * @throws BadRequestHttpException
     */
    public function refresh($data)
    {
        try {
            Db::startTrans();
            $model = new Authorizers();
            $model->where('platform_id', $data['platform_id'])->delete();
            $list = $this->app->getAuthorizers();
            $insert_data = [];
            foreach ($list['list'] as $item) {
                $program = $this->app->getAuthorizer($item['authorizer_appid']);
                $program_authorizer_info = $program['authorizer_info'];
                $program_authorization_info = $program['authorization_info'];
                $insert_data[] = [
                    'platform_id' => $data['platform_id'],
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
}