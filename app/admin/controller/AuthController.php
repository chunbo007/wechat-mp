<?php

namespace app\admin\controller;

use app\admin\model\User;
use support\Request;

/**
 * 用户登录
 */
class AuthController extends BaseController
{
    /**
     * 不需要登录的方法
     */
    protected $noNeedLogin = ['login', 'logout'];

    public function login(Request $request): \support\Response
    {
        $user = new User();
        return $user->login($request->post());
    }

    public function logout()
    {
        $user = new User();
        return $user->logout();
    }
}