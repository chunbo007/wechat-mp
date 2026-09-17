<?php

namespace app\service;

use PDO;
use PDOException;
use Redis;
use RedisException;

class InstallService
{
    /**
     * 安装锁文件路径
     */
    protected function getLockFile(): string
    {
        return runtime_path('install.lock');
    }

    /**
     * 检查是否已安装
     */
    public function isInstalled(): bool
    {
        return file_exists($this->getLockFile());
    }

    /**
     * 环境检测
     */
    public function checkEnvironment(): array
    {
        $result = [
            'php' => [],
            'extensions' => [],
            'functions' => [],
            'directories' => [],
            'is_windows' => false,
            'doc_url' => 'https://www.workerman.net/doc/webman/others/disable-function-check.html',
        ];

        // 检测是否为Windows环境
        $isWindows = DIRECTORY_SEPARATOR === '\\' || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $result['is_windows'] = $isWindows;

        // PHP版本
        $phpVersion = PHP_VERSION;
        $result['php'] = [
            'name' => 'PHP版本',
            'required' => '8.2.x',
            'current' => $phpVersion,
            'pass' => version_compare($phpVersion, '8.2.0', '>=') && version_compare($phpVersion, '8.3.0', '<')
        ];

        // 必需扩展
        $extensions = [
            'pdo_mysql' => 'PDO MySQL',
            'redis' => 'Redis',
            'curl' => 'cURL',
            'fileinfo' => 'Fileinfo',
            'mbstring' => 'Mbstring',
            'openssl' => 'OpenSSL',
            'json' => 'JSON',
            'xml' => 'XML',
        ];

        foreach ($extensions as $ext => $name) {
            $loaded = extension_loaded($ext);
            $result['extensions'][] = [
                'name' => $name,
                'required' => '必需',
                'current' => $loaded ? '已安装' : '未安装',
                'pass' => $loaded
            ];
        }

        // 函数可用性检查（Webman/Workerman必需）
        $requiredFunctions = [
            // 网络函数
            'stream_socket_server' => 'Stream Socket Server',
            'stream_socket_client' => 'Stream Socket Client',
            // 进程函数
            'proc_open' => 'Proc Open',
            'proc_get_status' => 'Proc Get Status',
            'proc_close' => 'Proc Close',
            // 执行函数
            'shell_exec' => 'Shell Exec',
            'exec' => 'Exec',
            // 环境函数
            'putenv' => 'Put Env',
            'getenv' => 'Get Env',
        ];

        // Linux环境才检查PCNTL和POSIX函数
        if (!$isWindows) {
            $linuxFunctions = [
                // PCNTL进程控制
                'pcntl_signal_dispatch' => 'PCNTL Signal Dispatch',
                'pcntl_signal' => 'PCNTL Signal',
                'pcntl_alarm' => 'PCNTL Alarm',
                'pcntl_fork' => 'PCNTL Fork',
                'pcntl_wait' => 'PCNTL Wait',
                // POSIX函数
                'posix_getuid' => 'POSIX Get UID',
                'posix_getpwuid' => 'POSIX Get PWUID',
                'posix_kill' => 'POSIX Kill',
                'posix_setsid' => 'POSIX Set SID',
                'posix_getpid' => 'POSIX Get PID',
                'posix_getpwnam' => 'POSIX Get PW Nam',
                'posix_getgrnam' => 'POSIX Get GR Nam',
                'posix_getgid' => 'POSIX Get GID',
                'posix_setgid' => 'POSIX Set GID',
                'posix_initgroups' => 'POSIX Init Groups',
                'posix_setuid' => 'POSIX Set UID',
                'posix_isatty' => 'POSIX Is TTY',
            ];
            $requiredFunctions = array_merge($requiredFunctions, $linuxFunctions);
        }

        foreach ($requiredFunctions as $func => $name) {
            $exists = function_exists($func);
            $result['functions'][] = [
                'name' => $name,
                'required' => '必需',
                'current' => $exists ? '可用' : '不可用',
                'pass' => $exists
            ];
        }

        // 目录权限
        $dirs = [
            'runtime' => runtime_path(),
            'public/storage' => base_path(false) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'storage',
        ];

        foreach ($dirs as $name => $path) {
            $writable = is_dir($path) && is_writable($path);
            $result['directories'][] = [
                'name' => $name,
                'required' => '可写',
                'current' => $writable ? '可写' : '不可写',
                'pass' => $writable
            ];
        }

        // .env文件
        $envFile = base_path(false) . DIRECTORY_SEPARATOR . '.env';
        $envExists = file_exists($envFile);
        $envWritable = !$envExists || is_writable($envFile);
        $result['directories'][] = [
            'name' => '.env 文件',
            'required' => '可写',
            'current' => $envWritable ? '可写' : '不可写',
            'pass' => $envWritable
        ];

        // 计算总状态
        $allPass = $result['php']['pass'];
        $allPass = $allPass && !in_array(false, array_column($result['extensions'], 'pass'), true);
        $allPass = $allPass && !in_array(false, array_column($result['functions'], 'pass'), true);
        $allPass = $allPass && !in_array(false, array_column($result['directories'], 'pass'), true);

        $result['pass'] = $allPass;

        return $result;
    }

    /**
     * 测试数据库连接
     */
    public function testDatabaseConnection(array $config): array
    {
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_TIMEOUT => 3,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // 检查数据库是否存在，不存在则尝试创建
            $stmt = $pdo->query("SELECT 1 FROM information_schema.schemata WHERE schema_name = '{$config['database']}'");
            if (!$stmt->fetch()) {
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            }

            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    /**
     * 测试Redis连接
     */
    public function testRedisConnection(array $config): array
    {
        try {
            $redis = new Redis();
            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? 6379;
            $password = $config['password'] ?? '';
            $database = $config['database'] ?? 0;

            $redis->connect($host, $port, 3);
            
            if (!empty($password)) {
                $redis->auth($password);
            }
            
            $redis->select($database);
            $redis->ping();
            $redis->close();

            return ['success' => true];
        } catch (RedisException $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    /**
     * 执行安装
     */
    public function executeInstall(array $data): array
    {
        try {
            // 1. 写入.env文件
            $envResult = $this->writeEnvFile($data);
            if (!$envResult['success']) {
                return $envResult;
            }

            // 2. 导入数据库
            $dbResult = $this->importDatabase($data);
            if (!$dbResult['success']) {
                return $dbResult;
            }

            // 3. 创建管理员账号
            $adminResult = $this->createAdmin($data);
            if (!$adminResult['success']) {
                return $adminResult;
            }

            // 4. 更新网站配置
            $configResult = $this->updateSiteConfig($data);
            if (!$configResult['success']) {
                return $configResult;
            }

            // 5. 创建安装锁
            file_put_contents($this->getLockFile(), date('Y-m-d H:i:s'));

            // 6. 重启服务
            $this->restartService();

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }

    /**
     * 写入.env文件
     */
    protected function writeEnvFile(array $data): array
    {
        try {
            $envPath = base_path(false) . DIRECTORY_SEPARATOR . '.env';
            
            $serverIp = $data['server_ip'] ?? '127.0.0.1';
            $webSite = $data['web_site'] ?? '';
            $cloudApiKey = $data['cloud_api_key'] ?? '';
            $cloudApiSecret = $data['cloud_api_secret'] ?? '';
            
            $content = <<<ENV
# 调试模式
APP_DEBUG = false

# 记录Sql
SQL_LOG = false

# 监控文件变动，用supervisor时创建守护进程时，这里填false
AUTO_RELOAD = false

# 服务器IP
SERVER_IP = {$serverIp}

# 网站地址
WEB_SITE = {$webSite}

# 数据库用户名
DB_USERNAME = {$data['db_username']}
# 数据库密码
DB_PASSWORD = {$data['db_password']}
# 数据库名
DB_DATABASE = {$data['db_database']}
# 数据库主机
DB_HOST = {$data['db_host']}
# 数据库端口
DB_PORT = {$data['db_port']}

# redis主机
REDIS_HOST = {$data['redis_host']}
# redis 端口
REDIS_PORT = {$data['redis_port']}
# redis 密码
REDIS_PASSWORD = {$data['redis_password']}
# redis 数据库
REDIS_DATABASE = {$data['redis_database']}
# redis 队列前缀
REDIS_QUEUE_PREFIX = webman_queue-

# 单个文件的大小限制
SINGLE_LIMIT = 25
# 所有文件的大小限制
TOTAL_LIMIT = 100

# 百度ocr
BAIDU_APP_ID = 
BAIDU_API_KEY = 
BAIDU_SECRET_KEY = 

# 演示账号id
DEMO_ID =

# 系统升级授权
CLOUD_API_URL = https://upgrade.isvapi.com
CLOUD_API_KEY = {$cloudApiKey}
CLOUD_API_SECRET = {$cloudApiSecret}
ENV;

            file_put_contents($envPath, $content);
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'msg' => '写入.env文件失败: ' . $e->getMessage()];
        }
    }

    /**
     * 导入数据库
     */
    protected function importDatabase(array $data): array
    {
        try {
            $dsn = "mysql:host={$data['db_host']};port={$data['db_port']};dbname={$data['db_database']};charset=utf8mb4";
            $pdo = new PDO($dsn, $data['db_username'], $data['db_password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // 获取所有SQL文件并按版本号排序
            $sqlFiles = $this->getSqlFiles();
            if (empty($sqlFiles)) {
                return ['success' => false, 'msg' => '未找到SQL文件'];
            }

            // 按顺序执行SQL文件
            foreach ($sqlFiles as $sqlFile) {
                $sql = file_get_contents($sqlFile);
                
                // 分割SQL语句
                $statements = array_filter(array_map('trim', explode(';', $sql)));
                
                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        $pdo->exec($statement);
                    }
                }
            }

            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'msg' => '数据库导入失败: ' . $e->getMessage()];
        }
    }

    /**
     * 获取按版本排序的SQL文件列表
     */
    protected function getSqlFiles(): array
    {
        $installDir = base_path(false) . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'install';
        
        if (!is_dir($installDir)) {
            return [];
        }

        $files = [];
        $iterator = new \DirectoryIterator($installDir);
        
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile() && $fileinfo->getExtension() === 'sql') {
                $filename = $fileinfo->getFilename();
                // 提取版本号，支持 v1.0.0.sql 或 1.0.0.sql 格式
                if (preg_match('/v?(\d+)\.(\d+)\.(\d+)\.sql$/', $filename, $matches)) {
                    $versionKey = sprintf('%03d%03d%03d', $matches[1], $matches[2], $matches[3]);
                    $files[$versionKey] = $fileinfo->getPathname();
                }
            }
        }

        // 按版本号排序
        ksort($files, SORT_STRING);
        
        return array_values($files);
    }
    
    /**
     * 创建管理员账号
     */
    protected function createAdmin(array $data): array
    {
        try {
            // 使用PDO直连,避免ThinkORM配置未加载的问题
            $dsn = "mysql:host={$data['db_host']};port={$data['db_port']};dbname={$data['db_database']};charset=utf8mb4";
            $pdo = new PDO($dsn, $data['db_username'], $data['db_password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            // 检查用户名是否已存在
            $stmt = $pdo->prepare('SELECT id FROM eb_system_user WHERE username = ?');
            $stmt->execute([$data['admin_username']]);
            $exists = $stmt->fetch();
            
            if ($exists) {
                // 删除已存在的用户
                $stmt = $pdo->prepare('DELETE FROM eb_system_user WHERE username = ?');
                $stmt->execute([$data['admin_username']]);
            }
            
            // 插入管理员用户
            $stmt = $pdo->prepare('
                INSERT INTO eb_system_user 
                (username, password, nickname, avatar, dept_id, phone, email, status, remark, expire_time, create_time, update_time)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $data['admin_username'],
                password_hash($data['admin_password'], PASSWORD_DEFAULT),
                '管理员',
                null,
                1,
                '',
                '',
                1,
                '',
                '2099-01-01 00:00:00',
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s'),
            ]);
            
            // 获取插入的用户ID
            $userId = $pdo->lastInsertId();
            
            // 插入角色关联
            $stmt = $pdo->prepare('INSERT INTO eb_system_user_role (user_id, role_id) VALUES (?, ?)');
            $stmt->execute([$userId, 2]);

            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'msg' => '创建管理员失败: ' . $e->getMessage()];
        }
    }

    /**
     * 更新网站配置
     */
    protected function updateSiteConfig(array $data): array
    {
        try {
            $webSite = $data['web_site'] ?? '';
            
            if (empty($webSite)) {
                return ['success' => true]; // 如果网站地址为空,跳过更新
            }
            
            // 使用PDO直连,避免ThinkORM配置未加载的问题
            $dsn = "mysql:host={$data['db_host']};port={$data['db_port']};dbname={$data['db_database']};charset=utf8mb4";
            $pdo = new PDO($dsn, $data['db_username'], $data['db_password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            // 更新 eb_system_config 表中 id=12 且 key=local_domain 的记录
            $stmt = $pdo->prepare('UPDATE eb_system_config SET value = ? WHERE id = 12 AND `key` = ?');
            $stmt->execute([$webSite, 'local_domain']);
            
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'msg' => '更新网站配置失败: ' . $e->getMessage()];
        }
    }

    /**
     * 重启服务
     */
    protected function restartService(): void
    {
        if (function_exists('posix_kill') && function_exists('posix_getppid') && \defined('SIGUSR1')){
            posix_kill(posix_getppid(), SIGUSR1);
        }
    }

    /**
     * 获取版本信息
     */
    public function getVersionInfo(): array
    {
        $versionFile = base_path(false) . DIRECTORY_SEPARATOR . 'version.json';
        
        if (!file_exists($versionFile)) {
            return ['success' => false, 'msg' => 'version.json文件不存在'];
        }
        
        $content = file_get_contents($versionFile);
        $versionData = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'msg' => 'version.json文件格式错误'];
        }
        
        if (!isset($versionData['version'])) {
            return ['success' => false, 'msg' => 'version.json中缺少version字段'];
        }
        
        return ['success' => true, 'version' => $versionData['version']];
    }
}
