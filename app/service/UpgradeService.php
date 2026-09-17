<?php
namespace app\service;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use support\think\Db;

class UpgradeService
{
    /**
     * @var string
     */
    protected $cloudApiUrl;

    /**
     * @var string
     */
    protected $cloudApiKey;

    /**
     * @var string
     */
    protected $cloudApiSecret;

    protected $projectCode;

    /**
     * @var Client
     */
    protected $httpClient;

    public function __construct($projectCode)
    {
        $this->cloudApiUrl = env('CLOUD_API_URL', 'https://upgrade.isvapi.com');
        $this->cloudApiKey = env('CLOUD_API_KEY', '');
        $this->cloudApiSecret = env('CLOUD_API_SECRET', '');
        $this->projectCode = $projectCode;
        $this->httpClient = new Client();
    }

    /**
     * 生成签名
     * @param array $params
     * @return string
     */
    protected function generateSignature(array $params): string
    {
        unset($params['signature']);
        ksort($params);
        
        $stringToSign = '';
        foreach ($params as $key => $value) {
            $stringToSign .= $key . '=' . $value . '&';
        }
        
        $stringToSign .= 'secret=' . $this->cloudApiSecret;
        
        return strtoupper(md5($stringToSign));
    }

    /** 开源插件使用升级服务器公开接口，不需要客户授权凭证。 */
    protected function isPublicFreeProject(string $projectCode): bool
    {
        return $projectCode !== '' && (bool)preg_match('/(?:^|_)free$/', $projectCode);
    }

    protected function requestOptions(array $requestParams, bool $public = false): array
    {
        if ($public) {
            return ['query' => $requestParams];
        }
        $timestamp = $requestParams['timestamp'] ?? time();
        return [
            'headers' => [
                'X-API-Key' => $this->cloudApiKey,
                'X-Timestamp' => $timestamp,
                'X-Signature' => $this->generateSignature($requestParams),
            ],
            'query' => $requestParams,
        ];
    }

    public function setCredentials(?string $apiKey, ?string $apiSecret): void
    {
        if ($apiKey !== null && $apiKey !== '') $this->cloudApiKey = $apiKey;
        if ($apiSecret !== null && $apiSecret !== '') $this->cloudApiSecret = $apiSecret;
    }

    protected function persistCredentials(?string $apiKey, ?string $apiSecret): void
    {
        if (!$apiKey || !$apiSecret) {
            return;
        }
        $envFile = base_path() . '/.env';
        if (!is_file($envFile)) {
            return;
        }
        $content = file_get_contents($envFile);
        foreach (['CLOUD_API_KEY' => $apiKey, 'CLOUD_API_SECRET' => $apiSecret] as $key => $value) {
            $line = $key . ' = ' . $value;
            if (preg_match('/^' . $key . '\\s*=.*$/m', $content)) {
                $content = preg_replace('/^' . $key . '\\s*=.*$/m', $line, $content);
            } else {
                $content .= PHP_EOL . $line . PHP_EOL;
            }
        }
        file_put_contents($envFile, $content);
    }

    /**
     * 获取项目补丁包列表 外部接口
     * @param array $params
     * @return array
     * @throws GuzzleException
     */
    public function getPatchList(array $params = []): array
    {
        $timestamp = time();
        $requestParams = array_merge($params, [
            'timestamp' => $timestamp,
        ]);
        
        $projectCode = (string)($requestParams['project_code'] ?? '');
        $this->appendFrameworkVersion($requestParams, $projectCode);
        $response = $this->httpClient->get($this->cloudApiUrl . '/api/external/packages/list',
            $this->requestOptions($requestParams, $this->isPublicFreeProject($projectCode)));

        $body = $response->getBody()->getContents();
        $result = json_decode($body, true) ?? [];
        
        return $result['data'] ?? [];
    }

    /**
    * 获取指定版本的补丁信息 外部接口
    * @param string $version 版本号
    * @param string $projectCode 项目代码
    * @return array
     */
    public function getPatchInfo(string $version, string $projectCode, ?string $apiKey = null, ?string $apiSecret = null): array
    {
        if (!$version) {
            return [];
        }
        $this->setCredentials($apiKey, $apiSecret);
        $timestamp = time();
        $requestParams = [
            'version' => $version,
            'project_code' => $projectCode,
            'timestamp' => $timestamp
        ];
        $this->appendFrameworkVersion($requestParams, $projectCode);
        
        $response = $this->httpClient->get($this->cloudApiUrl . '/api/external/packages/detail',
            $this->requestOptions($requestParams, $this->isPublicFreeProject($projectCode)));

        $body = $response->getBody()->getContents();
        $result = json_decode($body, true) ?? [];
        if ($result['code'] != 0){
            throw new Exception($result['message'] ?? '获取补丁信息失败', $result['code'] ?? 500);
        }
        
        return $result['data'] ?? [];
    }

    public function getNextPatchInfo(string $version, string $projectCode, ?string $apiKey = null, ?string $apiSecret = null): array
    {
        if (!$version) {
            return [];
        }
        $this->setCredentials($apiKey, $apiSecret);
        $timestamp = time();
        $requestParams = [
            'version' => $version,
            'project_code' => $projectCode,
            'timestamp' => $timestamp
        ];
        $this->appendFrameworkVersion($requestParams, $projectCode);
        
        $response = $this->httpClient->get($this->cloudApiUrl . '/api/external/packages/next',
            $this->requestOptions($requestParams, $this->isPublicFreeProject($projectCode)));

        $body = $response->getBody()->getContents();
        $result = json_decode($body, true) ?? [];
        if ($result['code'] != 0){
            throw new Exception($result['message'] ?? '获取补丁信息失败', $result['code'] ?? 500);
        }
        
        return $result['data'] ?? [];
    } 

    /**
     * 获取所有项目列表 外部接口
     * @return array
     * @throws GuzzleException
     */
    public function getAllProjects(): array
    {
        $response = $this->httpClient->get($this->cloudApiUrl . '/api/external/projects/all');

        $body = $response->getBody()->getContents();
        $result = json_decode($body, true) ?? [];
        
        return $result['data'] ?? [];
    }

    /**
     * 插件升级请求携带当前主框架版本，供升级服务器做最低版本拦截。
     */
    private function appendFrameworkVersion(array &$params, string $projectCode): void
    {
        if (strpos($projectCode, 'plugin_') !== 0 || !empty($params['framework_version'])) {
            return;
        }

        $versionFile = base_path() . '/version.json';
        if (!is_file($versionFile)) {
            return;
        }
        $data = json_decode((string)file_get_contents($versionFile), true) ?? [];
        if (!empty($data['version']) && preg_match('/^\d+\.\d+\.\d+$/', (string)$data['version'])) {
            $params['framework_version'] = (string)$data['version'];
        }
    }

    /**
     * 获取所有项目及其安装版本（合并接口）
     * @return array
     * @throws GuzzleException
     */
    public function getAllProjectsWithVersions(): array
    {
        // 只调用一次云端 API 获取项目列表
        $projects = $this->getAllProjects();

        // 遍历获取每个项目的本地安装版本
        foreach ($projects as &$project) {
            $projectCode = $project['project_code'] ?? '';
            if (!empty($projectCode)) {
                $project['current_version'] = $this->getInstalledVersion($projectCode);
            }
        }

        return $projects;
    }

    /**
     * 获取单个项目的已安装版本号
     * @param string $projectCode 项目代码
     * @return string
     */
    private function getInstalledVersion(string $projectCode): string
    {
        // 主程序
        if ($projectCode === 'isvapi') {
            $versionFile = base_path() . '/version.json';
            if (file_exists($versionFile)) {
                $content = file_get_contents($versionFile);
                $data = json_decode($content, true) ?? [];
                if (!empty($data['version'])) {
                    return $data['version'];
                }
            }
            return '未安装';
        }
        
        // 插件
        if (strpos($projectCode, 'plugin_') === 0) {
            // 去掉 plugin_ 前缀
            $pluginName = substr($projectCode, 7);
            $versionFile = base_path() . '/plugin/' . $pluginName . '/version.json';
            
            if (file_exists($versionFile)) {
                $content = file_get_contents($versionFile);
                $data = json_decode($content, true) ?? [];
                if (!empty($data['version'])) {
                    return $data['version'];
                }
            }
            return '未安装';
        }
        
        return '未知';
    }

    /**
     * 执行升级
     * @param string $targetVersion
     * @param string $projectCode
     * @return array
     * @throws Exception
     */
    public function executeUpgrade(string $targetVersion, string $projectCode, ?string $apiKey = null, ?string $apiSecret = null): array
    {
        $this->setCredentials($apiKey, $apiSecret);
        // 设置执行时间和内存限制
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        // 定义插件相关变量
        $isPlugin = strpos($projectCode, 'plugin_') === 0;
        $pluginName = $isPlugin ? substr($projectCode, 7) : null; // 去掉 'plugin_' 前缀
        $currentVersion = $this->getInstalledVersion($projectCode);
        $isPluginInstall = $isPlugin && $currentVersion === '未安装';
        $operationLock = null;
        
        $basePath = base_path();
        $runtimePath = runtime_path();
        $upgradePath = $runtimePath . '/upgrade';
        $logFile = $runtimePath . '/logs/upgrade_' . date('Ymd') . '.log';

        // 创建必要目录
        $this->ensureDirectory($upgradePath);
        $this->ensureDirectory(dirname($logFile));
        if ($isPlugin && $pluginName) {
            $operationLock = new PluginOperationLock($pluginName);
            $operationLock->acquire();
        }

        try {
            // 1. 获取补丁信息
            $this->updateUpgradeStatus('downloading', '获取补丁信息...', 0.1);
            $this->log($logFile, '获取补丁信息...');
            $patchInfo = $this->getPatchInfo($targetVersion, $projectCode);
            
            // 根据操作类型选择相应的URL和MD5字段
            $packageUrl = $isPluginInstall ? ($patchInfo['install_package_url'] ?? $patchInfo['package_url']) : $patchInfo['package_url'];
            $packageMd5 = $isPluginInstall ? ($patchInfo['install_md5'] ?? $patchInfo['md5']) : $patchInfo['md5'];
            
            $this->log($logFile, "开始" . ($isPluginInstall ? "插件安装" : ($isPlugin ? "插件升级" : ($isPluginInstall ? "安装" : "升级"))) . "到版本 {$targetVersion}" . ($isPlugin ? " (插件: {$pluginName})" : ""));
            
            if (!$patchInfo || empty($packageUrl)) {
                throw new Exception('补丁包信息不存在');
            } else if ($patchInfo['status'] == 3){
                throw new Exception('该版本只能手动升级');
            }

            // 2. 检查磁盘空间
            $this->updateUpgradeStatus('checking', '检查磁盘空间...', 0.15);
            $this->log($logFile, '检查磁盘空间...');
            $freeSpace = disk_free_space($basePath);
            $requiredSpace = ($patchInfo['file_size'] ?? 0) * 3; // 需要3倍空间
            if ($freeSpace < $requiredSpace) {
                throw new Exception('磁盘空间不足');
            }

            // 3. 下载补丁包
            $this->updateUpgradeStatus('downloading', '下载补丁包...', 0.2);
            $this->log($logFile, '下载补丁包...');
            $zipFile = $upgradePath . '/v' . $targetVersion . '.zip';
            $this->downloadPatchFile($packageUrl, $zipFile);

            // 4. 校验MD5
            $this->updateUpgradeStatus('verifying', '校验MD5...', 0.4);
            $this->log($logFile, '校验MD5...');
            $md5 = md5_file($zipFile);
            if ($md5 !== $packageMd5) {
                throw new Exception('MD5校验失败，文件可能已损坏');
            }

            // 5. 解压文件
            $this->updateUpgradeStatus('extracting', '解压文件...', 0.5);
            $this->log($logFile, '解压文件...');
            $extractPath = "{$upgradePath}/{$projectCode}/v{$targetVersion}";
            $this->extractZip($zipFile, $extractPath);

            // 插件安装前按 manifest/install.php 声明处理替换关系。
            if ($isPluginInstall && $pluginName) {
                $installManifest = "{$extractPath}/plugin/{$pluginName}/manifest/install.php";
                (new PluginLifecycleService())->prepareInstall($installManifest, $pluginName, $logFile);
            }

            // 6. 复制文件（普通文件到根目录，插件文件到插件目录）
            $this->updateUpgradeStatus('copying', '复制文件...', 0.6);
            $this->log($logFile, '复制文件到相应目录...');
            if ($isPlugin && $pluginName) {
                $this->validatePluginPackage($extractPath, $pluginName, $isPluginInstall);
            }
            $this->copyFiles($extractPath, $basePath);

            // 7. 执行主SQL
            $sqlFile = "{$extractPath}/database/install/v{$targetVersion}.sql";
            if (file_exists($sqlFile) && !$isPlugin) {
                $this->updateUpgradeStatus('executing_sql', '执行SQL...', 0.8);
                $this->log($logFile, '执行SQL...');
                $this->executeSQL($sqlFile);
            }

            // 8. 执行插件SQL
            if ($isPlugin) {
                $this->executePluginSql($extractPath, $pluginName, $logFile, $currentVersion, $targetVersion);
                // manifest 是公共资源的唯一来源，安装和升级都需要执行，且执行器保证幂等。
                if ($pluginName) {
                    $packageManifest = "{$extractPath}/plugin/{$pluginName}/manifest/install.php";
                    $installedManifest = "{$basePath}/plugin/{$pluginName}/manifest/install.php";
                    $installManifest = is_file($packageManifest) ? $packageManifest : $installedManifest;
                    (new PluginLifecycleService())->installManifest($installManifest, $pluginName, $logFile);
                }
            }

            // 9. 清理临时文件
            $this->updateUpgradeStatus('cleaning', '清理临时文件...', 0.9);
            $this->log($logFile, '清理临时文件...');
            $this->cleanupUpgradeFiles($zipFile, $extractPath);

            $this->log($logFile, "升级完成！");

            if (!$this->isPublicFreeProject($projectCode)) {
                $this->persistCredentials($apiKey, $apiSecret);
            }

            // 9. 重启服务
            $this->updateUpgradeStatus('restarting', '升级成功！', 1.0);
            $this->restartService();

            return [
                'success' => true,
                'message' => '升级成功，请重启服务',
                'version' => $targetVersion
            ];

        } catch (Exception $e) {
            $this->updateUpgradeStatus('failed', '升级失败: ' . $e->getMessage(), 0);
            $this->log($logFile, "升级失败: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '升级失败: ' . $e->getMessage()
            ];
        } finally {
            if ($operationLock) {
                $operationLock->release();
            }
        }
    }

    /**
     * 确保目录存在
     * @param string $dir
     */
    protected function ensureDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    /**
     * 写日志
     * @param string $logFile
     * @param string $message
     */
    protected function log(string $logFile, string $message): void
    {
        $time = date('Y-m-d H:i:s');
        $logMessage = "[{$time}] {$message}" . PHP_EOL;
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    /**
     * 下载补丁文件
     * @param string $url
     * @param string $savePath
     * @throws Exception
     */
    protected function downloadPatchFile(string $url, string $savePath): void
    {
        $response = $this->httpClient->get($url, [
            'sink' => $savePath,
            'timeout' => 300,
            'verify' => false
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new Exception('下载补丁包失败');
        }
    }

    /**
     * 解压ZIP文件
     * @param string $zipFile
     * @param string $extractPath
     * @throws Exception
     */
    protected function extractZip(string $zipFile, string $extractPath): void
    {
        // 同一版本目录可能被上一次失败或错误包解压过，必须先清空，避免旧插件文件混入本次安装。
        if (is_dir($extractPath)) {
            $this->deleteDirectory($extractPath);
        }
        $this->ensureDirectory($extractPath);

        $zip = new \ZipArchive();
        $result = $zip->open($zipFile);
        
        if ($result !== true) {
            throw new Exception('无法打开ZIP文件');
        }

        if (!$zip->extractTo($extractPath)) {
            $zip->close();
            throw new Exception('解压文件失败');
        }

        $zip->close();
    }



    /**
     * 复制文件到根目录
     * @param string $source
     * @param string $dest
     * @throws Exception
     */
    protected function copyFiles(string $source, string $dest): void
    {
        // 不应该被覆盖的目录和文件
        $excludes = [
            '.env',
            'storage',
            'upgrade.sql',
            'patch_info.json',
        ];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relativePath = substr($item->getPathname(), strlen($source) + 1);
            
            // 检查是否在排除列表中
            $shouldExclude = false;
            foreach ($excludes as $exclude) {
                if (strpos($relativePath, $exclude) === 0) {
                    $shouldExclude = true;
                    break;
                }
            }

            if ($shouldExclude) {
                continue;
            }

            // 直接复制到根目录
            $target = $dest . '/' . $relativePath;
            
            if ($item->isDir()) {
                $this->ensureDirectory($target);
            } else {
                $targetDir = dirname($target);
                $this->ensureDirectory($targetDir);
                
                // Windows下可能存在文件锁定问题，尝试多次
                $maxRetries = 3;
                $copied = false;
                for ($i = 0; $i < $maxRetries; $i++) {
                    if (@copy($item->getPathname(), $target)) {
                        $copied = true;
                        break;
                    }
                    sleep(1);
                }
                
                if (!$copied) {
                    throw new Exception("无法复制文件: {$relativePath}");
                }
            }
        }
    }

    /**
     * 获取目标文件路径
     * @param string $relativePath 相对路径
     * @param string $dest 目标根目录
     * @param bool $isPlugin 是否是插件操作
     * @param string|null $pluginName 插件名称
     * @return string 目标文件的完整路径
     */
    protected function getTargetPath(string $relativePath, string $dest, bool $isPlugin = false, ?string $pluginName = null): string
    {
        // 如果是插件安装/升级，直接复制到插件目录
        if ($isPlugin) {
            if ($pluginName) {
                $pluginBasePath = base_path() . '/plugin/' . $pluginName;
                return $pluginBasePath . '/' . $relativePath;
            }
        }
        
        // 非插件操作，直接复制到根目录
        return $dest . '/' . $relativePath;
    }

    /**
     * 执行SQL文件
     * @param string $sqlFile
     * @throws Exception
     */
    protected function executeSQL(string $sqlFile): void
    {
        if (!file_exists($sqlFile)) {
            return;
        }

        $sqlContent = file_get_contents($sqlFile);
        if (empty($sqlContent)) {
            return;
        }

        // 简单的SQL语句分割（按分号分割，忽略注释）
        $statements = $this->parseSQLStatements($sqlContent);

        // 使用事务
        Db::startTrans();
        
        try {
            foreach ($statements as $statement) {
                if (!empty(trim($statement))) {
                    Db::execute($statement);
                }
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw new Exception('SQL执行失败: ' . $e->getMessage());
        }
    }

    /**
     * 解析SQL语句
     * @param string $sql
     * @return array
     */
    protected function parseSQLStatements(string $sql): array
    {
        // 移除注释
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // 按分号分割
        $statements = explode(';', $sql);
        
        // 过滤空语句
        return array_filter(array_map('trim', $statements));
    }



    /**
     * 清理升级文件
     * @param string $zipFile
     * @param string $extractPath
     */
    protected function cleanupUpgradeFiles(string $zipFile, string $extractPath): void
    {
        // 删除ZIP文件
        if (file_exists($zipFile)) {
            @unlink($zipFile);
        }

        // 删除解压目录
        // if (is_dir($extractPath)) {
        //     $this->deleteDirectory($extractPath);
        // }
    }

    /**
     * 递归删除目录
     * @param string $dir
     */
    protected function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($items as $item) {
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }
        
        @rmdir($dir);
    }

    /**
     * 重启服务（延时5秒后执行）
     */
    protected function restartService(): void
    {
        if (function_exists('posix_kill') && function_exists('posix_getppid') && \defined('SIGUSR1')) {
            posix_kill(posix_getppid(), SIGUSR1);
        }
    }

    /**
     * 更新升级状态
     * @param string $status 状态: downloading, checking, verifying, extracting, copying, executing_sql, cleaning, restarting, failed
     * @param string $message 状态描述
     * @param float $progress 进度 0-1
     */
    protected function updateUpgradeStatus(string $status, string $message, float $progress): void
    {
        $statusData = [
            'status' => $status,
            'message' => $message,
            'progress' => $progress,
            'time' => time()
        ];
        
        $cacheKey = 'upgrade_status';
        \support\Redis::setex($cacheKey, 600, json_encode($statusData)); // 10分钟过期
    }

    /**
     * 获取升级状态
     * @return array
     */
    public function getUpgradeStatus(): array
    {
        $cacheKey = 'upgrade_status';
        $data = \support\Redis::get($cacheKey);
        
        if (!$data) {
            return [
                'status' => 'idle',
                'message' => '暂无升级任务',
                'progress' => 0
            ];
        }
        
        return json_decode($data, true) ?? [];
    }

    /**
     * 执行插件SQL文件
     * @param string $extractPath 解压目录
     * @param string $logFile 日志文件
     * @param string $currentVersion 版本号
     * @param string $targetVersion 目标版本号
     */
    protected function executePluginSql(string $extractPath, string $pluginName, string $logFile, string $currentVersion, string $targetVersion): void
    {
        (new PluginOwnershipService())->ensureSchema();
        // 在解压目录的 database 目录中查找 SQL 文件
        $databasePath = "{$extractPath}/plugin/{$pluginName}/database";
        if (!is_dir($databasePath)) {
            return;
        }

        // 如果是首次安装（版本号为未安装），执行所有版本的SQL文件
        if ($currentVersion === '未安装') {
            $this->executeAllPluginSql($databasePath, $logFile, 'database');
        } else {
            // 查找 v版本号.sql 文件
            $sqlFile = $databasePath . '/v' . $targetVersion . '.sql';
            if (file_exists($sqlFile)) {
                $this->log($logFile, "执行数据库SQL文件...");
                $this->executeSQL($sqlFile);
            }
        }
        (new PluginOwnershipService())->assign($pluginName);
        (new PluginOwnershipService())->validateDictionaryOwnership($pluginName);
    }

    /**
     * 校验插件包只能包含当前插件的后端与前端目录。
     */
    protected function validatePluginPackage(string $extractPath, string $pluginName, bool $requireFrontend): void
    {
        $pluginPath = $extractPath . '/plugin/' . $pluginName;
        if (!is_file($pluginPath . '/version.json')) {
            throw new Exception("插件包缺少 plugin/{$pluginName}/version.json");
        }

        $backendRoot = $extractPath . '/plugin';
        foreach (array_diff(scandir($backendRoot), ['.', '..']) as $entry) {
            if (is_dir($backendRoot . '/' . $entry) && $entry !== $pluginName) {
                throw new Exception("插件包包含其他后端插件目录: {$entry}");
            }
        }

        $frontendRoot = $extractPath . '/public/plugins';
        if (!is_dir($frontendRoot)) {
            if ($requireFrontend) {
                throw new Exception("插件包缺少独立前端资源: public/plugins/{$pluginName}");
            }
            return;
        }
        foreach (array_diff(scandir($frontendRoot), ['.', '..']) as $entry) {
            if (is_dir($frontendRoot . '/' . $entry) && $entry !== $pluginName) {
                throw new Exception("插件包包含其他前端插件目录: {$entry}");
            }
        }

        $manifestFile = $frontendRoot . '/' . $pluginName . '/manifest.json';
        $manifest = is_file($manifestFile) ? json_decode(file_get_contents($manifestFile), true) : null;
        if (($manifest['plugin_code'] ?? '') !== $pluginName || empty($manifest['entry'])) {
            throw new Exception("插件 {$pluginName} 的前端 manifest.json 无效");
        }
        if (!is_file(dirname($manifestFile) . '/' . $manifest['entry'])) {
            throw new Exception("插件 {$pluginName} 的前端入口文件不存在");
        }
    }

    /**
     * 执行插件目录下所有版本的SQL文件
     * @param string $databasePath database目录
     * @param string $logFile 日志文件
     * @param string $context 上下文描述
     */
    protected function executeAllPluginSql(string $databasePath, string $logFile, string $context): void
    {
        $sqlFiles = $this->getPluginSqlFiles($databasePath);
        if (empty($sqlFiles)) {
            return;
        }

        $this->log($logFile, "执行 {$context} 目录下的所有SQL文件...");
        
        foreach ($sqlFiles as $sqlFile) {
            $this->executeSQL($sqlFile);
        }
    }

    /**
     * 获取插件目录下按版本排序的SQL文件列表
     * @param string $databasePath database目录
     * @return array SQL文件路径数组
     */
    protected function getPluginSqlFiles(string $databasePath): array
    {
        if (!is_dir($databasePath)) {
            return [];
        }

        $files = [];
        $iterator = new \DirectoryIterator($databasePath);
        
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
}
