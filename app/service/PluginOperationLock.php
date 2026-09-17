<?php

namespace app\service;

use Exception;

class PluginOperationLock
{
    private $handle;
    private string $path;

    public function __construct(string $pluginCode)
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $pluginCode)) {
            throw new Exception('插件代码无效');
        }
        $dir = runtime_path('plugin_operations');
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new Exception('无法创建插件操作锁目录');
        }
        $this->path = $dir . '/' . $pluginCode . '.lock';
    }

    public function acquire(): void
    {
        $this->handle = fopen($this->path, 'c');
        if (!$this->handle || !flock($this->handle, LOCK_EX | LOCK_NB)) {
            throw new Exception('插件正在执行其他安装、升级或卸载操作');
        }
    }

    public function release(): void
    {
        if (is_resource($this->handle)) {
            flock($this->handle, LOCK_UN);
            fclose($this->handle);
            $this->handle = null;
        }
    }
}
