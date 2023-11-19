<?php

namespace app\admin\model;

use app\common\model\User as UserModel;
use Tinywan\Jwt\JwtToken;

class User extends UserModel
{
    public function login($params = []): \support\Response
    {
        $username = $params['username'];
        $password = $params['password'];
        $where = [
            'username' => $username,
            'password' => $password
        ];
        $user = $this->where($where)->find();
        if (!$user) {
            return error('用户名或密码错误');

        }

        $token = JwtToken::generateToken($user->toArray());
        $user['token'] = "{$token['token_type']} {$token['access_token']}";
        return success($user);
    }

    public function logout()
    {
        JwtToken::clear();
        return success();
    }
}