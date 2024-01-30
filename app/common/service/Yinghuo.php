<?php

namespace app\common\service;

class Yinghuo
{
    private string $baseUrl = '/index.php?s=';
    private string $accessToken = '';

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
        $result = $this->curl('/admin/passport/login', $data, false);
        if ($result['status'] == 200) {
            $this->accessToken = $result['data']['token'];
            return success($result['data'], 0, '登录成功', true);
        }
        return error($result['message'], $result['status'], [], true);
    }

    private function curl($cmd, $data, $needLogin = true, $method = 'POST')
    {
        try {
            $url = env('YINGHUO_SITE') . $this->baseUrl . $cmd;
            $header = [];
            if ($needLogin) {
                $header = ["Access-Token:{$this->accessToken}"];
            }
            $result = curlRequest($url, $data, $header, $method);
            return json_decode($result, true);
        } catch (\Exception $e) {
            return ['status' => -1, 'message' => '请求失败'];
        }

    }

    /**
     * 新增店铺
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
}