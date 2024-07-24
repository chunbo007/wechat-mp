<?php

namespace app\common\service;

use support\Log;
use support\Response;
use think\facade\Db;

class Yinghuo
{
    private string $baseUrl = '/index.php?s=';
    private string $adminAccessToken = '';
    private string $storeAccessToken = '';

    /**
     * 运营平台登录
     * @param $username
     * @param $password
     * @return array
     */
    public function adminLogin($username, $password): array
    {
        $data = [
            'username' => $username,
            'password' => $password
        ];
        $result = $this->curl('/admin/passport/login', $data, 'admin', false);
        if ($result['status'] == 200) {
            $this->adminAccessToken = $result['data']['token'];
            return success($result['data'], 0, '登录成功', true);
        }
        return error($result['message'], $result['status'], [], true);
    }

    /**
     * 运营平台新增店铺
     * @param $storeName
     * @param $username
     * @param $password
     * @param $sort
     * @return array|mixed
     */
    public function addStore($storeName, $username, $password, $sort = 100)
    {
        $data = [
            'form' => [
                'store_name' => $storeName,
                'user_name' => $username,
                'password' => $password,
                'password_confirm' => $password,
                'sort' => $sort,
            ]
        ];
        return $this->curl('/admin/store/add', $data);
    }

    /**
     * 运营平台设置功能模块
     * @param $storeId
     * @return array|mixed
     */
    public function setModule($storeId)
    {
        $data = [
            "storeId" => $storeId,
            "form" => [
                "moduleKeys" => [
                    "store",
                    "store-page",
                    "store-page-category",
                    "store-style",
                    "store-style-theme",
                    "goods",
                    "goods-copy",
                    "goods-import",
                    "order",
                    "order-updatePrice",
                    "order-printer",
                    "order-export",
                    "order-eorder",
                    "user",
                    "user-grade",
                    "user-balance",
                    "content",
                    "content-article",
                    "content-help",
                    "market",
                    "market-coupon",
                    "market-recharge",
                    "market-points",
                    "market-promote",
                    "market-recommended",
                    "market-fullFree",
                    "statistics",
                    "statistics-statistics",
                    "client",
                    "client-mpWeixin",
                    "client-wxofficial",
                    "client-h5",
                    "apps",
                    "apps-dealer",
                    "apps-bargain",
                    "apps-groupon",
                    "apps-sharp",
                    "apps-live",
                    "apps-eorder",
                    "apps-collector",
                    "setting",
                    "setting-customer",
                    "setting-printer"
                ]
            ]
        ];
        return $this->curl('/admin/store.module/edit', $data);
    }

    /**
     * 运营平台获取商城登录token
     * @return array|Response
     */
    public function superLogin($storeId)
    {
        $result = $this->curl("/admin/store/superLogin&storeId=$storeId", [], 'admin', true, 'GET');
        if ($result['status'] == 200) {
            $this->storeAccessToken = $result['data']['token'];
            return success($result['data'], 0, '登录成功', true);
        }
        return error($result['message'], $result['status'], [], true);
    }

    /**
     * 商城设置默认secret
     * @param $appid
     * @return array|mixed
     */
    public function settingWxAppInfo($appid)
    {
        $data = [
            "key" => "basic",
            "form" => [
                "enabled" => true,
                "app_id" => $appid,
                "app_secret" => "fastRegisterApp",
                "enableShipping" => false,
            ]
        ];
        return $this->curl('/store/client.wxapp.setting/update', $data, 'store');
    }

    /**
     * 商城设置上传CDN配置
     * @return array|mixed
     */
    public function settingUpload()
    {
        $data = [
            "key" => "storage",
            "form" => [
                "default" => "qiniu",
                "engine" => [
                    "qiniu" => [
                        "bucket" => env('QINIU_BUCKET'),
                        "access_key" => env('QINIU_ACCESS_KEY'),
                        "secret_key" => env('QINIU_SECRET_KEY'),
                        "domain" => env('QINIU_DOMAIN')
                    ],
                    "aliyun" => [
                        "bucket" => "",
                        "access_key_id" => "",
                        "access_key_secret" => "",
                        "domain" => "http://"
                    ],
                    "qcloud" => [
                        "bucket" => "",
                        "region" => "",
                        "secret_id" => "",
                        "secret_key" => "",
                        "domain" => "http://"
                    ]
                ]
            ]
        ];
        return $this->curl('/store/setting/update', $data, 'store');
    }

    /**
     * 商城设置注册设置
     * @return array|mixed
     */
    public function settingRegister()
    {
        $data = [
            "key" => "register",
            "form" => [
                "isOauthMpweixin" => 1,
                "isForceBindMpweixin" => 0,
                "isOauthMobileMpweixin" => 1,
                "isOauthWxofficial" => 0,
                "isForceBindWxofficial" => 1,
                "isOauthMpAlipay" => 1,
                "isForceBindMpAlipay" => 1,
                "registerMethod" => 10,
                "isManualBind" => 0,
                "isPersonalMpweixin" => 1
            ]
        ];
        return $this->curl('/store/setting/update', $data, 'store');
    }

    /**
     * 通过SQL配置其他内容
     * @param $storeId
     * @return void
     */
    public function setOther($storeId)
    {
        $mysqlDumpFile = base_path() . '/yinghuo.sql';
        if (!is_file($mysqlDumpFile)) {
            return;
        }
        $time = time();
        foreach (explode(';', file_get_contents($mysqlDumpFile)) as $sql) {
            if ($sql = trim($sql)) {
                try {
                    $sql = str_replace('STOREID', $storeId, $sql);
                    $sql = str_replace('TIME', $time, $sql);
                    Db::connect('yinghuo')->execute($sql);
                } catch (\Exception $e) {
                }
            }
        }

        // 获取delivery_id，添加包邮地区明细
        $delivery_id = Db::connect('yinghuo')->table('yoshop_delivery')->max('delivery_id');
        $mysqlDumpFile = base_path() . '/yinghuo_delivery.sql';
        if (!is_file($mysqlDumpFile)) {
            return;
        }
        $sql = file_get_contents($mysqlDumpFile);
        $sql = str_replace('STOREID', $storeId, $sql);
        $sql = str_replace('TIME', $time, $sql);
        $sql = str_replace('DELIVERYID', $delivery_id, $sql);
        try {
            Db::connect('yinghuo')->execute($sql);
        } catch (\Exception $e) {
        }
    }

    private function curl($cmd, $data, $type = "admin", $needLogin = true, $method = 'POST')
    {
        try {
            $url = env('YINGHUO_SITE') . $this->baseUrl . $cmd;
            if ($needLogin && $type === 'admin') {
                $header = ["Access-Token:{$this->adminAccessToken}"];
            } else {
                $header = ["Access-Token:{$this->storeAccessToken}"];
            }
            $result = curlRequest($url, $data, $header, $method);
            Log::info('萤火接口响应:' . $result);
            return json_decode($result, true);
        } catch (\Exception $e) {
            return ['status' => -1, 'message' => '请求失败'];
        }

    }
}