<?php

namespace app\controller;

use support\Request;
use support\Response;
use app\service\InstallService;

class InstallController
{
    protected InstallService $service;

    public function __construct()
    {
        $this->service = new InstallService();
    }

    /**
     * 获取安装状态
     */
    public function status(Request $request): Response
    {
        $installed = $this->service->isInstalled();
        return json(['code' => 200, 'data' => ['installed' => $installed]]);
    }

    /**
     * 环境检测
     */
    public function check(Request $request): Response
    {
        if ($this->service->isInstalled()) {
            return json(['code' => 400, 'msg' => '系统已安装']);
        }

        $result = $this->service->checkEnvironment();
        return json(['code' => 200, 'data' => $result]);
    }

    /**
     * 测试数据库连接
     */
    public function dbTest(Request $request): Response
    {
        if ($this->service->isInstalled()) {
            return json(['code' => 400, 'msg' => '系统已安装']);
        }

        $data = $request->post();
        $required = ['host', 'port', 'database', 'username', 'password'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return json(['code' => 400, 'msg' => "{$field} 不能为空"]);
            }
        }

        $result = $this->service->testDatabaseConnection($data);
        if ($result['success']) {
            return json(['code' => 200, 'msg' => '连接成功']);
        }
        return json(['code' => 400, 'msg' => $result['msg']]);
    }

    /**
     * 测试Redis连接
     */
    public function redisTest(Request $request): Response
    {
        if ($this->service->isInstalled()) {
            return json(['code' => 400, 'msg' => '系统已安装']);
        }

        $data = $request->post();
        $result = $this->service->testRedisConnection($data);
        if ($result['success']) {
            return json(['code' => 200, 'msg' => '连接成功']);
        }
        return json(['code' => 400, 'msg' => $result['msg']]);
    }

    /**
     * 执行安装
     */
    public function execute(Request $request): Response
    {
        if ($this->service->isInstalled()) {
            return json(['code' => 400, 'msg' => '系统已安装']);
        }

        $data = $request->post();
        
        // 验证必填项
        $required = [
            'db_host', 'db_port', 'db_database', 'db_username', 'db_password',
            'admin_username', 'admin_password',
            'server_ip', 'web_site'
        ];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return json(['code' => 400, 'msg' => "{$field} 不能为空"]);
            }
        }

        // 验证密码长度
        if (strlen($data['admin_password']) < 6) {
            return json(['code' => 400, 'msg' => '管理员密码至少6位']);
        }

        $result = $this->service->executeInstall($data);
        
        if ($result['success']) {
            return json(['code' => 200, 'msg' => '安装成功']);
        }
        return json(['code' => 400, 'msg' => $result['msg']]);
    }

    /**
     * 获取版本信息
     */
    public function getVersionInfo(Request $request): Response
    {
        $result = $this->service->getVersionInfo();
        if ($result['success']) {
            return json(['code' => 200, 'data' => $result['version']]);
        }
        return json(['code' => 400, 'msg' => $result['msg']]);
    }
}
