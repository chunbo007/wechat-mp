<?php

namespace app\common\service;

use app\admin\model\User as UserModel;
use Tinywan\Jwt\JwtToken;

class User extends BaseServices
{
    public function changePassword($originPassword, $newPassword, $confirmPassword)
    {
        if ($newPassword !== $confirmPassword) {
            return error('新密码和确认密码输入不一致');
        }

        $dbPassword = JwtToken::getExtend()['password'];
        if ($dbPassword !== $originPassword) {
            return error('原始密码输入错误');
        }

        $user = new UserModel;
        $user->update(['password' => $newPassword], ['id' => JwtToken::getExtend()['id']]);
        return success();
    }

}