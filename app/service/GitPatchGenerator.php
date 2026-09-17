<?php

namespace app\service;

use Exception;

/**
 * Git补丁包生成器
 * 用于从Git版本库中提取两个版本之间的差异文件和SQL变更
 */
class GitPatchGenerator
{
    protected $repoPath;

    protected $pluginPath;
    protected $pluginName;

    protected $isPluginPatch;

    public function __construct($repoPath = null, $pluginName = null)
    {
        $this->repoPath = $repoPath ?: base_path();
        $this->pluginName = $pluginName ?: null;
        $this->isPluginPatch = !empty($pluginName);
        $this->pluginPath = $this->isPluginPatch ? "{$this->repoPath}/plugin/{$this->pluginName}" : null;
    }
    
    /**
     * 生成补丁包
     * @param string $fromVersion 起始版本
     * @param string $toVersion 结束版本
     * @param string $outputPath 输出路径
     * @return array
     * @throws Exception
     */
    public function generatePatch(string $fromVersion, string $toVersion, string $outputPath): array
    {
        // 验证Git仓库
        if (!$this->isGitRepository()) {
            throw new Exception('当前目录不是Git仓库');
        }
        
        // 验证起始版本标签是否存在
        if (!$this->tagExists($fromVersion)) {
            throw new Exception("起始版本标签 {$fromVersion} 不存在");
        }
        
        // 验证目标版本：如果是标签则验证存在性，如果是提交哈希则验证格式
        if (strpos($toVersion, 'commit-') === 0) {
            // 对于提交哈希，我们只需要验证它看起来像一个有效的哈希
            $commitHash = substr($toVersion, 7); // 移除 'commit-' 前缀
            if (strlen($commitHash) < 4 || !ctype_xdigit($commitHash)) {
                throw new Exception("无效的提交哈希格式: {$toVersion}");
            }
        } else if (!$this->tagExists($toVersion)) {
            throw new Exception("目标版本标签 {$toVersion} 不存在");
        }
        
        // 获取两个版本之间的差异文件
        $changedFiles = $this->getChangedFilesBetweenVersions($fromVersion, $toVersion);
        // 创建输出目录
        if (!is_dir($outputPath)) {
            mkdir($outputPath, 0755, true);
        }
        
        // 过滤文件类型
        $phpFiles = [];
        $sqlFiles = [];
        $otherFiles = [];
        
        foreach ($changedFiles as $file) {
            $fullPath = $this->isPluginPatch ? $this->pluginPath . '/' . $file : $this->repoPath . '/' . $file;
            if (!file_exists($fullPath)) {
                continue;
            }
            
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            if (in_array($extension, ['php', 'js', 'vue', 'html', 'css', 'json', 'xml', 'txt', 'md'])) {
                $phpFiles[] = $file;
            } else if ($extension === 'sql') {
                $sqlFiles[] = $file;
            } else {
                $otherFiles[] = $file;
            }
        }
        
        // 直接从database/install目录查找与目标版本同名的SQL文件
        $sqlChanges = $this->getTargetVersionSqlFile($toVersion);
        
        // 生成补丁包文件
        $patchInfo = [
            'from_version' => $fromVersion,
            'to_version' => $toVersion,
            'generated_at' => date('Y-m-d H:i:s'),
            'changed_files' => $changedFiles,
            'php_files' => $phpFiles,
            'sql_files' => $sqlFiles,
            'other_files' => $otherFiles,
            'sql_changes_count' => count($sqlChanges),
            'is_plugin_patch' => $this->isPluginPatch,
            'plugin_name' => $this->pluginName,
        ];
        
        // 保存SQL变更到单独的文件
        // if (!empty($sqlChanges)) {
        //     $sqlFilePath = $outputPath . '/' . $toVersion . '.sql';
        //     $sqlContent = implode("\n\n", $sqlChanges);
        //     file_put_contents($sqlFilePath, $sqlContent);
        // }
        
        // 复制PHP和其他类型的变更文件
        $filesCopied = $this->copyChangedFiles($changedFiles, $this->isPluginPatch ? $this->pluginPath : $this->repoPath, $outputPath);
        
        // 检测是否有composer文件变动，如果有则复制整个vendor目录
        $hasComposerChanges = false;
        foreach ($changedFiles as $file) {
            if ($file === 'composer.json' || $file === 'composer.lock') {
                $hasComposerChanges = true;
                break;
            }
        }
        
        if ($hasComposerChanges) {
            $this->copyVendorDirectory($this->repoPath, $outputPath);
            $patchInfo['has_composer_changes'] = true;
            $patchInfo['vendor_copied'] = true;
        }
        
        // 生成补丁包信息文件
        $infoFilePath = "{$outputPath}/patch_info.json";
        file_put_contents($infoFilePath, json_encode($patchInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        // 生成版本文件
        $versionFilePath = $this->isPluginPatch
            ? "{$outputPath}/plugin/{$this->pluginName}/version.json"
            : "{$outputPath}/version.json";
        file_put_contents(
            $versionFilePath,
            json_encode(['version' => ltrim($toVersion, 'v')], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL
        );
        
        // 创建ZIP压缩包
        $zipFileName = $outputPath . '/patch_' . $fromVersion . '_to_' . $toVersion . '.zip';
        
        // 如果是插件补丁，则创建plugin目录结构再压缩
        // if ($isPluginPatch) {
        //     $this->createPluginZipPackage($outputPath, $zipFileName, $pluginName);
        // } else {
        //     $this->createZipPackage($outputPath, $zipFileName);
        // }
        // $this->createZipPackage($outputPath, $zipFileName);
        
        return [
            'success' => true,
            'message' => '补丁包生成成功',
            'patch_info' => $patchInfo,
            'output_path' => $outputPath,
            'zip_file' => $zipFileName,
            'files_copied' => $filesCopied,
            'is_plugin_patch' => $this->isPluginPatch,
            'plugin_name' => $this->pluginName,
        ];
    }
    
    /**
     * 检查是否为Git仓库
     * @return bool
     */
    protected function isGitRepository(): bool
    {
        $gitDir = $this->repoPath . '/.git';
        return is_dir($gitDir) || is_file($gitDir); // 支持子模块的情况
    }
    
    /**
     * 检查标签是否存在
     * @param string $tagName
     * @return bool
     */
    protected function tagExists(string $tagName): bool
    {
        $path = $this->isPluginPatch ? $this->pluginPath : $this->repoPath;
        $command = "cd " . escapeshellarg($path) . " && git tag -l \"{$tagName}\" 2>&1";
        $output = shell_exec($command);
        
        if ($output === false || $output === null) {
            return false;
        }
        
        $result = trim($output);
        return !empty($result) && $result === $tagName;
    }
    
    /**
     * 获取两个版本之间的变更文件列表
     * @param string $fromVersion
     * @param string $toVersion
     * @return array
     */
    protected function getChangedFilesBetweenVersions(string $fromVersion, string $toVersion): array
    {
        // 处理提交哈希格式的目标版本
        $actualToVersion = $toVersion;
        if (strpos($toVersion, 'commit-') === 0) {
            $actualToVersion = substr($toVersion, 7); // 移除 'commit-' 前缀
        }
        
        $path = $this->isPluginPatch ? $this->pluginPath : $this->repoPath;
        $command = "cd " . escapeshellarg($path) . " && git diff --name-only {$fromVersion}..{$actualToVersion} 2>&1";
        $output = shell_exec($command);
        
        if ($output === false || $output === null) {
            throw new Exception("执行Git命令失败");
        }
        
        // 检查是否有错误信息
        if (stripos($output, 'fatal:') !== false || stripos($output, 'error:') !== false) {
            throw new Exception("Git命令执行出错: " . trim($output));
        }
        
        $files = array_filter(
            array_map('trim', explode("\n", $output)),
            function($file) {
                return !empty($file);
            }
        );
        
        return array_values($files);
    }
    
    /**
     * 获取目标版本对应的SQL文件内容
     * 直接从database/install目录查找与目标版本同名的文件
     * @param string $targetVersion
     * @return array
     */
    protected function getTargetVersionSqlFile(string $targetVersion): array
    {
        $sqlChanges = [];
        
        // 构造目标版本SQL文件路径
        $installDir = $this->isPluginPatch ? $this->pluginPath . '/database' : $this->repoPath . '/database/install';
        $versionFileName = $targetVersion . '.sql';
        $targetSqlFile = $installDir . '/' . $versionFileName;
        // 检查文件是否存在
        if (file_exists($targetSqlFile)) {
            $content = file_get_contents($targetSqlFile);
            if (!empty(trim($content))) {
                $sqlChanges[] = "-- SQL changes for version: {$targetVersion}\n" . $content;
            }
        } 
        return $sqlChanges;
    }
    
    /**
     * 复制vendor目录到输出目录
     * @param string $sourceDir
     * @param string $targetDir
     * @throws Exception
     */
    protected function copyVendorDirectory(string $sourceDir, string $targetDir): void
    {
        $vendorSource = $sourceDir . '/vendor';
        $vendorTarget = $targetDir . '/vendor';
        
        if (!is_dir($vendorSource)) {
            throw new Exception('源目录中未找到vendor目录');
        }
        
        // 创建目标vendor目录
        if (!is_dir($vendorTarget)) {
            mkdir($vendorTarget, 0755, true);
        }
        
        // 递归复制整个vendor目录
        $this->recursiveCopy($vendorSource, $vendorTarget);
    }
    
    /**
     * 递归复制目录
     * @param string $source
     * @param string $target
     * @throws Exception
     */
    protected function recursiveCopy(string $source, string $target): void
    {
        if (!is_dir($source)) {
            throw new Exception("源目录不存在: {$source}");
        }
        
        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }
        
        $files = scandir($source);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $sourcePath = $source . '/' . $file;
            $targetPath = $target . '/' . $file;
            
            if (is_dir($sourcePath)) {
                $this->recursiveCopy($sourcePath, $targetPath);
            } else {
                if (!copy($sourcePath, $targetPath)) {
                    throw new Exception("复制文件失败: {$sourcePath} -> {$targetPath}");
                }
            }
        }
    }
    
    /**
     * 复制变更的文件到输出目录
     * @param array $changedFiles
     * @param string $sourceDir
     * @param string $targetDir
     * @return array
     */
    protected function copyChangedFiles(array $changedFiles, string $sourceDir, string $targetDir): array
    {
        $copiedFiles = [];
        
        foreach ($changedFiles as $file) {
            // 排除不需要打包的文件
            $excludePatterns = [
                '/\.git/',
                '/\.env/',
                '/runtime/',
                '/\.DS_Store/',
                '/Thumbs\.db/'
            ];
            
            $shouldExclude = false;
            foreach ($excludePatterns as $pattern) {
                if (preg_match($pattern, $file)) {
                    $shouldExclude = true;
                    break;
                }
            }
            
            if ($shouldExclude) {
                continue;
            }
            
            $sourceFile = $sourceDir . '/' . $file;
            // $targetFile = $targetDir . '/' . $file;
            $targetFile = $this->isPluginPatch ? $targetDir . '/plugin/' . $this->pluginName . '/' . $file : $targetDir . '/' . $file;
            
            if (file_exists($sourceFile)) {
                $targetDirName = dirname($targetFile);
                if (!is_dir($targetDirName)) {
                    mkdir($targetDirName, 0755, true);
                }
                
                if (copy($sourceFile, $targetFile)) {
                    $copiedFiles[] = $file;
                }
            }
        }
        
        return $copiedFiles;
    }
    
    /**
     * 创建ZIP压缩包
     * @param string $sourceDir
     * @param string $zipFileName
     */
    protected function createZipPackage(string $sourceDir, string $zipFileName): void
    {
        $zip = new \ZipArchive();
        
        if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    // 正确计算相对路径，确保保留目录结构
                    $relativePath = str_replace('\\', '/', substr($filePath, strlen($sourceDir) + 1));
                    
                    $zip->addFile($filePath, $relativePath);
                }
            }
            
            $zip->close();
        } else {
            throw new Exception("无法创建ZIP文件: {$zipFileName}");
        }
    }
    
    /**
     * 为插件创建ZIP压缩包，将内容放入plugin子目录
     * @param string $sourceDir
     * @param string $zipFileName
     */
    protected function createPluginZipPackage(string $sourceDir, string $zipFileName): void
    {
        $zip = new \ZipArchive();
        
        if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    // 计算相对于源目录的路径
                    $relativePath = str_replace('\\', '/', substr($filePath, strlen($sourceDir) + 1));
                    
                    // 检查是否是特定文件，这些文件应该直接放在ZIP根目录而不是plugin目录下
                    $rootLevelFiles = ['patch_info.json', 'version.json'];
                    $isRootLevelFile = in_array($relativePath, $rootLevelFiles);
                    
                    if ($isRootLevelFile) {
                        // 这些文件直接放在ZIP根目录
                        $zipPath = $relativePath;
                    } else {
                        // 其他文件放在plugin/{pluginName}/目录下
                        $zipPath = 'plugin/' . $this->pluginName . '/' . $relativePath;
                    }
                    
                    $zip->addFile($filePath, $zipPath);
                }
            }
            
            $zip->close();
        } else {
            throw new Exception("无法创建插件ZIP文件: {$zipFileName}");
        }
    }
    
    /**
     * 获取所有Git标签
     * @return array
     */
    public function getAllTags(): array
    {
        if (!$this->isGitRepository()) {
            return [];
        }
        
        $command = "cd {$this->repoPath} && git tag --sort=-v:refname";
        $output = shell_exec($command);
        
        if ($output === null) {
            return [];
        }
        
        $tags = array_filter(
            array_map('trim', explode("\n", $output)),
            function($tag) {
                return !empty($tag);
            }
        );
        
        return array_values($tags);
    }
    
    /**
     * 获取最新的几个标签
     * @param int $limit
     * @return array
     */
    public function getRecentTags(int $limit = 10): array
    {
        $allTags = $this->getAllTags();
        return array_slice($allTags, 0, $limit);
    }
    
    /**
     * 获取最新提交的信息
     * @return array
     * @throws Exception
     */
    public function getLatestCommitInfo(): array
    {
        if (!$this->isGitRepository()) {
            throw new Exception('当前目录不是Git仓库');
        }
        
        $command = "cd " . escapeshellarg($this->repoPath) . " && git log -1 --pretty=format:\"%H|%an|%ae|%at|%s\" 2>&1";
        $output = shell_exec($command);
        
        if ($output === false || $output === null) {
            throw new Exception('执行Git命令失败');
        }
        
        $output = trim($output);
        if (empty($output)) {
            throw new Exception('无法获取最新提交信息');
        }
        
        // 检查是否有错误信息
        if (stripos($output, 'fatal:') !== false || stripos($output, 'error:') !== false) {
            throw new Exception('Git命令执行出错: ' . $output);
        }
        
        $parts = explode('|', $output);
        return [
            'hash' => $parts[0] ?? '',
            'author_name' => $parts[1] ?? '',
            'author_email' => $parts[2] ?? '',
            'timestamp' => $parts[3] ?? '',
            'message' => $parts[4] ?? ''
        ];
    }
}
