<?php

namespace app\admin\controller;

use support\Request;
use app\admin\model\User;

/**
 * 用户登录
 */
class AuthController extends BaseController
{
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