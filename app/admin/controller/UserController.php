<?php

namespace app\admin\controller;

class UserController extends BaseController
{
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
}