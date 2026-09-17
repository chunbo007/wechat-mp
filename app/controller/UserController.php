<?php

namespace app\controller;

use app\service\Email;
use app\service\Message;
use plugin\saiadmin\utils\Captcha;
use support\Request;
use support\Response;
use app\common\OpenController;
use plugin\saiadmin\app\logic\system\SystemUserLogic;
use plugin\saiadmin\app\validate\system\SystemUserValidate;

class UserController extends OpenController
{
    protected SystemUserValidate $validate;
    public function __construct()
    {
        $this->logic = new SystemUserLogic();
        $this->validate = new SystemUserValidate();
        parent::__construct();
    }

    /**
     * 注册
     * @param Request $request
     * @return Response
     */
    public function register(Request $request): Response
    {

        $uuid = $request->post('uuid', '');
        $code = $request->post('code', '');
        $captcha = new Captcha();
        if (!$captcha->checkCaptcha($uuid, $code)) {
            return $this->fail('验证码错误');
        }
        $request->setPost('dept_id', 1);
        $request->setPost('role_ids',[14]);
        $request->setPost('status',1);
        $request->setPost('expire_time', date('Y-m-d H:i:s', strtotime('+15 day')));
        $data = $request->post();
        if (!$this->validate->scene('save')->check($data)) {
            return $this->fail($this->validate->getError());
        }
        $result = $this->logic->add($data);
        if ($result) {
            $time = date('Y-m-d H:i:s');
            $markdown = <<<EOT
             - <font color="#dd00dd">消息类型：用户注册</font>
             - <font color="#dd00dd">推送时间： $time</font>
             - <font color="#dd00dd">用户名：{$data['username']} </font>
            EOT;
            Message::send($markdown);
            return $this->success('操作成功');
        } else {
            return $this->fail('操作失败');
        }
    }

    /**
     * 忘记密码
     * @param Request $request
     * @return Response
     */
    public function sendForgetEmailCode(Request $request): Response
    {
        $uuid = $request->post('uuid', '');
        $code = $request->post('code', '');
        // 验证验证码
        $captcha = new Captcha();
        if (!$captcha->checkCaptcha($uuid, $code)) {
            return $this->fail('验证码错误');
        }
        $email = $request->post('email', '');
        // 检查是否输入邮箱
        if (!$email) {
            return $this->fail('邮箱不能为空');
        }
        // 检查邮箱格式
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->fail('邮箱格式错误');
        }
        // 验证邮箱是否存在
        $user = $this->logic->where('email', $email)->find();
        if (!$user) {
            return $this->fail('该邮箱未注册');
        }
        // 创建验证码
        $result = $captcha->numberCaptcha($email, 6);
        if ($result['result'] !== 1) {
            return $this->fail($result['message']);
        }
        // 发送邮件
        try {
            Email::send($email, $result['code'], '小程序管理平台密码找回');
            return $this->success(['uuid' => $result['uuid']], '验证码发送成功，10分钟内有效，请前往邮箱查看');
        } catch (\Exception $e){
            return $this->fail($e->getMessage());
        }
    }

    /**
     * 重置密码
     * @param Request $request
     * @return Response
     */
    public function resetPassword(Request $request): Response
    {
        $password = $request->post('password', '');
        $confirmPassword = $request->post('confirmPassword', '');
        $email = $request->post('email', '');
        $emailCode = $request->post('email_code', '');
        // 检查参数
        if (!$password || !$emailCode || !$email) {
            return $this->fail('参数错误');
        }
        // 检查邮箱格式
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->fail('邮箱格式错误');
        }
        if ($password !== $confirmPassword) {
            return $this->fail('两次密码不一致');
        }
        // 检查邮箱验证码是否正确
        $captcha = new Captcha();
        if (!$captcha->checkCaptcha($email, $emailCode)) {
            return $this->fail('验证码错误');
        }
        // 重置密码
        $this->logic->where('email', $email)->update(['password' => password_hash($password, PASSWORD_DEFAULT)]);
        return $this->success();
    }
}