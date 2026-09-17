<?php
namespace app\service;
use plugin\saiadmin\app\model\system\SystemMail;
use plugin\saiadmin\service\EmailService;
use plugin\saiadmin\utils\Arr;
use app\common\OpenController;
use support\exception\BusinessException;

/**
 * 发送邮件消息
 */
Class Email extends OpenController{
    public static function send($email, $code, $subject): bool
    {
        $content = "<h1>验证码：{code}</h1><p>如您未发起过此邮件，请忽略</p>";
        $template = [
            'code' => $code
        ];
        $config = EmailService::getConfig();
        $model = SystemMail::create([
            'gateway' => Arr::getConfigValue($config,'Host'),
            'from' => Arr::getConfigValue($config,'From'),
            'email' => $email,
            'code' => $code,
        ]);
        try {
            $result = EmailService::sendByTemplate($email, $subject, $content, $template);
            if (!empty($result)) {
                $model->status = 'failure';
                $model->response = $result;
                $model->save();
                throw new BusinessException('发送失败');
            } else {
                $model->status = 'success';
                $model->save();
                return true;
            }
        } catch (\Exception $e) {
            $model->status = 'failure';
            $model->response = $e->getMessage();
            $model->save();
            throw new BusinessException($e->getMessage());
        }
    }
}