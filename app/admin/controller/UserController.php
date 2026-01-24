<?php

namespace app\admin\controller;

use app\common\service\User as UserService;
use support\Request;

class UserController extends BaseController
{
    public $noNeedLogin = ['nginxTest'];

    public function nginxTest(){
        return success([
            'module' => 'admin',
            'path' => '/admin',
            'message' => 'Nginx配置正确，/admin路由可正常访问'
        ]);
    }

    public function info(){
        return success([
            'name' => 'admin',
            'avatar' => '/avatar2.jpg',
            'role' => [
                "permissions" => [
                    [
                        "permissionId" => "*",
                        "permissionName" => "管理员",
                        "actionEntitySet" => []
                    ]
                ]
            ]
        ]);
    }

    public function changePassword(Request $request)
    {
        $originalPassword = $request->input('original_password');
        $new_password = $request->input('new_password');
        $new_password_confirm = $request->input('new_password_confirm');
        $service = new UserService;
        return $service->changePassword($originalPassword, $new_password, $new_password_confirm);
    }
}