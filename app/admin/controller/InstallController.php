<?php

namespace app\admin\controller;

use support\Request;
use support\Response;
use PDO;
use PDOException;

class InstallController extends BaseController
{
    public $noNeedLogin = ['index', 'step', 'checkEnv', 'testDb', 'install', 'checkInstall'];

    private $installLockFile;
    private $envFile;
    private $sqlFile;
    private $installDataFile;

    public function __construct()
    {
        $this->installLockFile = base_path() . '/runtime/install.lock';
        $this->envFile = base_path() . '/.env';
        $this->sqlFile = base_path() . '/install.sql';
        $this->installDataFile = base_path() . '/runtime/install_data.json';
    }

    public function index()
    {
        if ($this->isInstalled()) {
            return redirect('/admin/login');
        }
        return redirect('/install');
    }

    public function step(Request $request)
    {
        $step = $request->input('step', 1);
        return success($this->getStepData($step));
    }

    public function checkEnv()
    {
        $requirements = [
            'php_version' => [
                'name' => 'PHP版本',
                'required' => '>= 8.1.0',
                'current' => PHP_VERSION,
                'status' => version_compare(PHP_VERSION, '8.1.0', '>='),
                'type' => 'php'
            ],
            'pdo_mysql' => [
                'name' => 'PDO MySQL扩展',
                'required' => '必须',
                'current' => extension_loaded('pdo_mysql') ? '已安装' : '未安装',
                'status' => extension_loaded('pdo_mysql'),
                'type' => 'extension'
            ],
            'json' => [
                'name' => 'JSON扩展',
                'required' => '必须',
                'current' => extension_loaded('json') ? '已安装' : '未安装',
                'status' => extension_loaded('json'),
                'type' => 'extension'
            ],
            'curl' => [
                'name' => 'CURL扩展',
                'required' => '必须',
                'current' => extension_loaded('curl') ? '已安装' : '未安装',
                'status' => extension_loaded('curl'),
                'type' => 'extension'
            ],
            'simplexml' => [
                'name' => 'SimpleXML扩展',
                'required' => '必须',
                'current' => extension_loaded('simplexml') ? '已安装' : '未安装',
                'status' => extension_loaded('simplexml'),
                'type' => 'extension'
            ],
            'mbstring' => [
                'name' => 'Mbstring扩展',
                'required' => '必须',
                'current' => extension_loaded('mbstring') ? '已安装' : '未安装',
                'status' => extension_loaded('mbstring'),
                'type' => 'extension'
            ],
            'runtime_writable' => [
                'name' => 'runtime目录可写',
                'required' => '必须',
                'current' => is_writable(base_path() . '/runtime') ? '可写' : '不可写',
                'status' => is_writable(base_path() . '/runtime'),
                'type' => 'dir'
            ],
            'env_writable' => [
                'name' => '.env文件可写',
                'required' => '必须',
                'current' => $this->checkEnvWritable() ? '可写' : '不可写',
                'status' => $this->checkEnvWritable(),
                'type' => 'file'
            ]
        ];

        $pass = true;
        foreach ($requirements as $item) {
            if (!$item['status']) {
                $pass = false;
                break;
            }
        }

        return success([
            'pass' => $pass,
            'requirements' => array_values($requirements)
        ]);
    }

    public function testDb(Request $request)
    {
        $host = $request->post('host', '127.0.0.1');
        $port = $request->post('port', '3306');
        $username = $request->post('username');
        $password = $request->post('password');
        $database = $request->post('database');

        try {
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5
            ]);

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            $pdo->exec("USE `{$database}`");

            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

            return success('数据库连接成功');
        } catch (PDOException $e) {
            return error('数据库连接失败: ' . $e->getMessage());
        }
    }

    public function install(Request $request)
    {
        $step = $request->post('step', 1);

        switch ($step) {
            case 2:
                return $this->stepDatabase($request);
            case 3:
                return $this->stepAdmin($request);
            case 4:
                return $this->stepSystem($request);
            case 5:
                return $this->stepInstall($request);
            default:
                return error('无效的步骤');
        }
    }

    private function stepDatabase(Request $request)
    {
        $dbConfig = [
            'host' => $request->post('host'),
            'port' => $request->post('port'),
            'database' => $request->post('database'),
            'username' => $request->post('username'),
            'password' => $request->post('password')
        ];

        foreach ($dbConfig as $key => $value) {
            if (empty($value)) {
                return error('请填写完整的数据库配置');
            }
        }

        try {
            $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$dbConfig['database']}`");

            $this->saveInstallData(['db_config' => $dbConfig]);

            return success('数据库配置保存成功');
        } catch (PDOException $e) {
            return error('数据库连接失败: ' . $e->getMessage());
        }
    }

    private function stepAdmin(Request $request)
    {
        $username = $request->post('username');
        $password = $request->post('password');
        $password_confirm = $request->post('password_confirm');

        if (empty($username) || empty($password)) {
            return error('请填写完整的管理员信息');
        }

        if (strlen($username) < 3 || strlen($username) > 20) {
            return error('用户名长度为3-20个字符');
        }

        if (strlen($password) < 6) {
            return error('密码长度不能少于6位');
        }

        if ($password !== $password_confirm) {
            return error('两次输入的密码不一致');
        }

        $this->saveInstallData([
            'admin' => [
                'username' => $username,
                'password' => md5($password)
            ]
        ], true);

        return success('管理员信息保存成功');
    }

    private function stepSystem(Request $request)
    {
        $siteUrl = $request->post('site_url');

        if (empty($siteUrl)) {
            return error('请填写网站域名');
        }

        if (!filter_var($siteUrl, FILTER_VALIDATE_URL)) {
            return error('请输入有效的URL地址');
        }

        $this->saveInstallData([
            'site_url' => rtrim($siteUrl, '/')
        ], true);

        return success('系统配置保存成功');
    }

    private function stepInstall(Request $request)
    {
        $installData = $this->getInstallData();

        if (!$installData || empty($installData['db_config']) || empty($installData['admin']) || empty($installData['site_url'])) {
            return error('安装信息不完整，请重新开始');
        }

        $dbConfig = $installData['db_config'];
        $admin = $installData['admin'];
        $siteUrl = $installData['site_url'];

        try {
            $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $sql = file_get_contents($this->sqlFile);
            $pdo->exec($sql);

            $stmt = $pdo->prepare("UPDATE `user` SET username = ?, password = ? WHERE id = 1");
            $stmt->execute([$admin['username'], $admin['password']]);

            $this->writeEnvFile($dbConfig, $siteUrl);

            file_put_contents($this->installLockFile, date('Y-m-d H:i:s'));

            if (file_exists($this->installDataFile)) {
                unlink($this->installDataFile);
            }

            return success('安装成功');
        } catch (PDOException $e) {
            return error('安装失败: ' . $e->getMessage());
        }
    }

    private function writeEnvFile($dbConfig, $siteUrl)
    {
        $envContent = "APP_DEBUG = true\n\n";
        $envContent .= "# mysql\n";
        $envContent .= "DB_HOST = {$dbConfig['host']}\n";
        $envContent .= "DB_PORT = {$dbConfig['port']}\n";
        $envContent .= "DB_NAME = {$dbConfig['database']}\n";
        $envContent .= "DB_USER = {$dbConfig['username']}\n";
        $envContent .= "DB_PASSWORD = {$dbConfig['password']}\n\n";
        $envContent .= "# 网站外网域名 例如 https://www.example.com\n";
        $envContent .= "SITE_URL = '{$siteUrl}'\n";

        file_put_contents($this->envFile, $envContent);
    }

    private function checkEnvWritable()
    {
        $envFile = base_path() . '/.env';
        if (!file_exists($envFile)) {
            return is_writable(base_path());
        }
        return is_writable($envFile);
    }

    public function checkInstall()
    {
        return success([
            'installed' => $this->isInstalled()
        ]);
    }

    private function isInstalled()
    {
        return file_exists($this->installLockFile);
    }

    private function getStepData($step)
    {
        $data = [
            'current' => $step,
            'total' => 5,
            'title' => $this->getStepTitle($step)
        ];
        return $data;
    }

    private function getStepTitle($step)
    {
        $titles = [
            1 => '环境检测',
            2 => '数据库配置',
            3 => '管理员设置',
            4 => '系统配置',
            5 => '安装执行'
        ];
        return $titles[$step] ?? '';
    }

    private function getInstallData()
    {
        if (!file_exists($this->installDataFile)) {
            return null;
        }
        $content = file_get_contents($this->installDataFile);
        return json_decode($content, true);
    }

    private function saveInstallData($data, $merge = false)
    {
        $installData = $merge ? $this->getInstallData() : [];
        if (!is_array($installData)) {
            $installData = [];
        }
        $installData = array_merge($installData, $data);
        file_put_contents($this->installDataFile, json_encode($installData));
    }
}
