<?php

namespace app\service;

use Exception;
use support\think\Db;

class PluginLifecycleService
{
    private PluginOwnershipService $ownership;

    public function __construct()
    {
        $this->ownership = new PluginOwnershipService();
    }

    public function preview(string $pluginCode): array
    {
        $this->assertPlugin($pluginCode);
        $data = $this->ownership->preview($pluginCode);
        $data['plugin_path'] = base_path() . '/plugin/' . $pluginCode;
        $data['installed'] = is_dir($data['plugin_path']);
        $data['frontend_path'] = public_path() . '/plugins/' . $pluginCode;
        $data['frontend_installed'] = is_file($data['frontend_path'] . '/manifest.json');
        return $data;
    }

    /**
     * 执行插件安装清单中声明的替换关系。
     *
     * 该方法在新插件文件复制前调用，确保被替换插件仍然可以读取自己的卸载 SQL。
     */
    public function prepareInstall(string $manifestFile, string $pluginCode, string $logFile = ''): void
    {
        $this->assertPlugin($pluginCode);
        $config = $this->loadManifest($manifestFile);
        foreach ((array)($config['replaces'] ?? []) as $replacePlugin) {
            if (!is_string($replacePlugin) || !preg_match('/^[a-zA-Z0-9_-]+$/', $replacePlugin)) {
                throw new Exception('插件替换配置无效');
            }
            if ($replacePlugin === $pluginCode) {
                throw new Exception("插件 {$pluginCode} 不能替换自身");
            }
            $replacePath = base_path() . '/plugin/' . $replacePlugin;
            if (!is_dir($replacePath)) {
                continue;
            }
            if ($logFile) {
                $this->log($logFile, "安装 {$pluginCode} 前卸载 {$replacePlugin}...");
            }
            $this->uninstall($replacePlugin, 'UNINSTALL ' . $replacePlugin);
        }
    }

    /**
     * 按插件 manifest/install.php 注册公共资源。
     *
     * 业务表仍由 database/*.sql 管理；本方法只处理菜单、计划任务和字典。
     */
    public function installManifest(string $manifestFile, string $pluginCode, string $logFile = ''): array
    {
        $this->assertPlugin($pluginCode);
        if (!is_file($manifestFile)) {
            return [];
        }
        $config = $this->loadManifest($manifestFile);
        $this->ownership->ensureSchema();

        Db::startTrans();
        try {
            $dictCodes = array_map(
                static fn(array $dict): string => (string)($dict['code'] ?? ''),
                (array)($config['dicts'] ?? [])
            );
            // 先补齐历史公共资源的归属，再按新清单删除已移除的资源。
            $this->ownership->assign($pluginCode, $dictCodes);
            $this->syncMenus((array)($config['menus'] ?? []), $pluginCode);
            $this->syncCrontabs((array)($config['crontabs'] ?? []), $pluginCode);
            $this->syncDicts((array)($config['dicts'] ?? []), $pluginCode);
            $result = [
                'menus' => $this->installMenus($config['menus'] ?? [], $pluginCode),
                'crontabs' => $this->installCrontabs((array)($config['crontabs'] ?? []), $pluginCode),
                'dict_types' => $this->installDicts((array)($config['dicts'] ?? []), $pluginCode),
            ];
            $this->ownership->validateDictionaryOwnership($pluginCode);
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            throw new Exception('插件安装清单执行失败: ' . $e->getMessage(), 0, $e);
        }

        if ($logFile) {
            $this->log($logFile, "已执行 {$pluginCode} 插件安装清单");
        }
        return $result;
    }

    /**
     * 仅移除插件清单管理的公共资源。
     *
     * 用于本地开发环境重置菜单、计划任务和字典；不会执行卸载 SQL，
     * 也不会删除插件源码、前端资源或任何插件业务表。
     */
    public function removeManifestResources(string $manifestFile, string $pluginCode, string $logFile = ''): array
    {
        $this->assertPlugin($pluginCode);
        $config = $this->loadManifest($manifestFile);
        $this->ownership->ensureSchema();

        Db::startTrans();
        try {
            $dictCodes = array_map(
                static fn(array $dict): string => (string)($dict['code'] ?? ''),
                (array)($config['dicts'] ?? [])
            );
            // 兼容在引入 plugin_code 前已写入数据库的历史公共资源。
            $this->ownership->assign($pluginCode, $dictCodes);
            $before = $this->ownership->preview($pluginCode);
            $this->syncMenus([], $pluginCode);
            $this->syncCrontabs([], $pluginCode);
            $this->syncDicts([], $pluginCode);
            Db::commit();
        } catch (\Throwable $e) {
            Db::rollback();
            throw new Exception('插件公共资源卸载失败: ' . $e->getMessage(), 0, $e);
        }

        if ($logFile) {
            $this->log($logFile, "已移除 {$pluginCode} 插件公共资源");
        }

        return [
            'menus' => $before['menus'],
            'role_menus' => $before['role_menus'],
            'crontabs' => $before['crontabs'],
            'dict_types' => $before['dict_types'],
            'dict_data' => $before['dict_data'],
        ];
    }

    /**
     * 删除新清单中已经移除的菜单及角色授权。
     */
    private function syncMenus(array $config, string $pluginCode): void
    {
        $desired = [];
        $file = $config['file'] ?? null;
        if ($file) {
            foreach ($this->loadResourceFile($file, '菜单') as $menu) {
                if (!is_array($menu) || empty($menu['code'])) {
                    throw new Exception('菜单缺少 code');
                }
                $desired[(string)$menu['code']] = true;
            }
        }

        $ownedMenus = Db::table('eb_system_menu')->where('plugin_code', $pluginCode)->select();
        $staleMenus = [];
        foreach ($ownedMenus as $menu) {
            if (!isset($desired[(string)$menu['code']])) {
                $staleMenus[] = $menu;
            }
        }
        // 子菜单先删除，避免数据库存在菜单层级约束时删除父级失败。
        usort($staleMenus, static function (array $left, array $right): int {
            return substr_count((string)($right['level'] ?? ''), ',') <=> substr_count((string)($left['level'] ?? ''), ',');
        });
        foreach ($staleMenus as $menu) {
            $menuId = (int)$menu['id'];
            Db::table('eb_system_role_menu')->where('menu_id', $menuId)->delete();
            Db::table('eb_system_menu')->where('id', $menuId)->delete();
        }
    }

    /**
     * 删除新清单中已经移除的计划任务。
     */
    private function syncCrontabs(array $crontabs, string $pluginCode): void
    {
        $desired = [];
        foreach ($crontabs as $task) {
            if (!is_array($task) || empty($task['target'])) {
                throw new Exception('计划任务缺少 target');
            }
            $desired[(string)$task['target']] = true;
        }

        foreach (Db::table('eb_system_crontab')->where('plugin_code', $pluginCode)->select() as $task) {
            if (!isset($desired[(string)$task['target']])) {
                Db::table('eb_system_crontab')->where('id', (int)$task['id'])->delete();
            }
        }
    }

    /**
     * 删除新清单中已经移除的字典类型及字典明细。
     */
    private function syncDicts(array $dicts, string $pluginCode): void
    {
        $desiredTypes = [];
        $desiredData = [];
        foreach ($dicts as $dict) {
            if (!is_array($dict) || empty($dict['code']) || empty($dict['name'])) {
                throw new Exception('数据字典缺少 code 或 name');
            }
            $code = (string)$dict['code'];
            $desiredTypes[$code] = true;
            $values = [];
            if (!empty($dict['data_file'])) {
                foreach ($this->loadResourceFile($dict['data_file'], "字典 {$code}") as $item) {
                    if (!is_array($item) || !array_key_exists('value', $item)) {
                        throw new Exception("字典 {$code} 明细缺少 value");
                    }
                    $values[(string)$item['value']] = true;
                }
            }
            $desiredData[$code] = $values;
        }

        foreach (Db::table('eb_system_dict_type')->where('plugin_code', $pluginCode)->select() as $type) {
            $code = (string)$type['code'];
            $typeId = (int)$type['id'];
            if (!isset($desiredTypes[$code])) {
                Db::table('eb_system_dict_data')->where('type_id', $typeId)->delete();
                Db::table('eb_system_dict_type')->where('id', $typeId)->delete();
                continue;
            }
            foreach (Db::table('eb_system_dict_data')->where('type_id', $typeId)->where('plugin_code', $pluginCode)->select() as $item) {
                if (!isset($desiredData[$code][(string)$item['value']])) {
                    Db::table('eb_system_dict_data')->where('id', (int)$item['id'])->delete();
                }
            }
        }
    }

    public function uninstall(string $pluginCode, string $confirmation): array
    {
        $this->assertPlugin($pluginCode);
        if ($confirmation !== 'UNINSTALL ' . $pluginCode) {
            throw new Exception('卸载确认文本不正确');
        }

        $pluginPath = base_path() . '/plugin/' . $pluginCode;
        $sqlFile = $pluginPath . '/database/uninstall.sql';
        if (!is_file($sqlFile)) {
            throw new Exception('插件未提供 database/uninstall.sql，无法安全卸载');
        }

        $lock = new PluginOperationLock($pluginCode);
        $lock->acquire();
        try {
            $this->ownership->assign($pluginCode);
            $preview = $this->ownership->preview($pluginCode);
            $this->executeSql($sqlFile);
            $this->ownership->validateRemoved($pluginCode);
            if (is_dir($pluginPath)) {
                $this->deleteDirectory($pluginPath, base_path() . '/plugin');
            }
            $frontendPath = public_path() . '/plugins/' . $pluginCode;
            if (is_dir($frontendPath)) {
                $this->deleteDirectory($frontendPath, public_path() . '/plugins');
            }
        } finally {
            $lock->release();
        }

        return [
            'success' => true,
            'plugin_code' => $pluginCode,
            'deleted' => $preview,
        ];
    }

    public function reinstall(string $pluginCode, string $version, string $confirmation, ?string $apiKey = null, ?string $apiSecret = null): array
    {
        $this->uninstall($pluginCode, $confirmation);
        return (new UpgradeService('isvapi'))->executeUpgrade(ltrim($version, 'v'), 'plugin_' . $pluginCode, $apiKey, $apiSecret);
    }

    private function executeSql(string $file): void
    {
        $sql = file_get_contents($file);
        if ($sql === false || trim($sql) === '') {
            return;
        }
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        $db = \support\think\Db::class;
        $db::startTrans();
        try {
            foreach ($statements as $statement) {
                $db::execute($statement);
            }
            $db::commit();
        } catch (\Throwable $e) {
            $db::rollback();
            throw new Exception('插件卸载 SQL 执行失败: ' . $e->getMessage(), 0, $e);
        }
    }

    private function loadManifest(string $manifestFile): array
    {
        if (!is_file($manifestFile)) {
            return [];
        }
        $config = require $manifestFile;
        if (!is_array($config)) {
            throw new Exception('插件安装清单必须返回数组');
        }
        return $config;
    }

    private function installMenus(array $config, string $pluginCode): int
    {
        $file = $config['file'] ?? null;
        if (!$file) {
            return 0;
        }
        $menus = $this->loadResourceFile($file, '菜单');
        $menuIds = [];
        $pending = array_values($menus);
        while ($pending) {
            $progress = false;
            foreach ($pending as $index => $menu) {
                if (!is_array($menu)) {
                    throw new Exception('菜单资源格式无效');
                }
                $code = (string)($menu['code'] ?? '');
                $name = (string)($menu['name'] ?? '');
                if (!$code || !$name) {
                    throw new Exception('菜单缺少 code 或 name');
                }
                $existing = Db::table('eb_system_menu')->where('code', $code)->find();
                $parentCode = $menu['parent_code'] ?? null;
                $parent = $parentCode ? Db::table('eb_system_menu')->where('code', $parentCode)->find() : null;
                if ($parentCode && !$parent) {
                    continue;
                }
                if ($existing) {
                    if (!empty($existing['plugin_code']) && $existing['plugin_code'] !== $pluginCode) {
                        throw new Exception("菜单 {$code} 已属于其他插件");
                    }
                    $id = (int)$existing['id'];
                    $parentId = $parent ? (int)$parent['id'] : 0;
                    $level = $parent ? trim((string)$parent['level'] . ',' . $parentId, ',') : '0';
                    Db::table('eb_system_menu')->where('id', $id)->update([
                        'parent_id' => $parentId,
                        'level' => $level,
                        'name' => $name,
                        'code' => $code,
                        'icon' => $menu['icon'] ?? '',
                        'route' => $menu['route'] ?? ($menu['path'] ?? ''),
                        'component' => $menu['component'] ?? null,
                        'redirect' => $menu['redirect'] ?? null,
                        'is_hidden' => (int)($menu['is_hidden'] ?? 1),
                        'is_layout' => (int)($menu['is_layout'] ?? 1),
                        'type' => $menu['type'] ?? 'M',
                        'generate_id' => (int)($menu['generate_id'] ?? 0),
                        'generate_key' => $menu['generate_key'] ?? null,
                        'status' => (int)($menu['status'] ?? 1),
                        'sort' => (int)($menu['sort'] ?? 0),
                        'remark' => $menu['remark'] ?? '',
                        'plugin_code' => $pluginCode,
                    ]);
                } else {
                    $parentId = $parent ? (int)$parent['id'] : 0;
                    $level = $parent ? trim((string)$parent['level'] . ',' . $parentId, ',') : '0';
                    $data = [
                        'parent_id' => $parentId,
                        'level' => $level,
                        'name' => $name,
                        'code' => $code,
                        'icon' => $menu['icon'] ?? '',
                        'route' => $menu['route'] ?? ($menu['path'] ?? ''),
                        'component' => $menu['component'] ?? null,
                        'redirect' => $menu['redirect'] ?? null,
                        'is_hidden' => (int)($menu['is_hidden'] ?? 1),
                        'is_layout' => (int)($menu['is_layout'] ?? 1),
                        'type' => $menu['type'] ?? 'M',
                        'generate_id' => (int)($menu['generate_id'] ?? 0),
                        'generate_key' => $menu['generate_key'] ?? null,
                        'status' => (int)($menu['status'] ?? 1),
                        'sort' => (int)($menu['sort'] ?? 0),
                        'remark' => $menu['remark'] ?? '',
                        'created_by' => 1,
                        'updated_by' => 1,
                        'create_time' => date('Y-m-d H:i:s'),
                        'update_time' => date('Y-m-d H:i:s'),
                        'plugin_code' => $pluginCode,
                    ];
                    $id = (int)Db::table('eb_system_menu')->insertGetId($data);
                }
                $menuIds[$code] = $id;
                unset($pending[$index]);
                $progress = true;
            }
            if (!$progress) {
                $unresolved = array_map(
                    static fn(array $menu): string => (string)($menu['code'] ?? ''),
                    $pending
                );
                throw new Exception('菜单父级不存在或存在循环引用: ' . implode(', ', $unresolved));
            }
        }

        foreach ((array)($config['grant_roles'] ?? []) as $roleId) {
            foreach ($menuIds as $menuId) {
                $exists = Db::table('eb_system_role_menu')
                    ->where('role_id', (int)$roleId)
                    ->where('menu_id', $menuId)
                    ->find();
                if (!$exists) {
                    Db::table('eb_system_role_menu')->insert([
                        'role_id' => (int)$roleId,
                        'menu_id' => $menuId,
                    ]);
                }
            }
        }
        return count($menuIds);
    }

    private function installCrontabs(array $crontabs, string $pluginCode): int
    {
        $count = 0;
        foreach ($crontabs as $task) {
            if (!is_array($task) || empty($task['target'])) {
                throw new Exception('计划任务缺少 target');
            }
            $target = (string)$task['target'];
            $data = [
                'name' => (string)($task['name'] ?? $target),
                'type' => (int)($task['type'] ?? 3),
                'target' => $target,
                'parameter' => (string)($task['parameter'] ?? ''),
                'rule' => (string)($task['rule'] ?? ''),
                'singleton' => (int)($task['singleton'] ?? 1),
                'status' => (int)($task['status'] ?? 1),
                'remark' => (string)($task['remark'] ?? ''),
                'plugin_code' => $pluginCode,
            ];
            $existing = Db::table('eb_system_crontab')->where('target', $target)->find();
            if ($existing) {
                if (!empty($existing['plugin_code']) && $existing['plugin_code'] !== $pluginCode) {
                    throw new Exception("计划任务 {$target} 已属于其他插件");
                }
                Db::table('eb_system_crontab')->where('id', (int)$existing['id'])->update($data);
            } else {
                $data['created_by'] = 1;
                $data['updated_by'] = 1;
                $data['create_time'] = date('Y-m-d H:i:s');
                $data['update_time'] = date('Y-m-d H:i:s');
                Db::table('eb_system_crontab')->insert($data);
            }
            $count++;
        }
        return $count;
    }

    private function installDicts(array $dicts, string $pluginCode): int
    {
        $count = 0;
        foreach ($dicts as $dict) {
            if (!is_array($dict) || empty($dict['code']) || empty($dict['name'])) {
                throw new Exception('数据字典缺少 code 或 name');
            }
            $code = (string)$dict['code'];
            $existing = Db::table('eb_system_dict_type')->where('code', $code)->whereNull('delete_time')->find();
            if ($existing) {
                if (!empty($existing['plugin_code']) && $existing['plugin_code'] !== $pluginCode) {
                    throw new Exception("字典 {$code} 已属于其他插件");
                }
                $typeId = (int)$existing['id'];
                Db::table('eb_system_dict_type')->where('id', $typeId)->update(['plugin_code' => $pluginCode]);
            } else {
                $typeId = (int)Db::table('eb_system_dict_type')->insertGetId([
                    'name' => (string)$dict['name'],
                    'code' => $code,
                    'status' => (int)($dict['status'] ?? 1),
                    'remark' => (string)($dict['remark'] ?? ''),
                    'created_by' => 1,
                    'updated_by' => 1,
                    'create_time' => date('Y-m-d H:i:s'),
                    'update_time' => date('Y-m-d H:i:s'),
                    'delete_time' => null,
                    'plugin_code' => $pluginCode,
                ]);
            }
            $dataFile = $dict['data_file'] ?? null;
            if ($dataFile) {
                foreach ($this->loadResourceFile($dataFile, "字典 {$code}") as $item) {
                    if (!is_array($item) || !array_key_exists('value', $item) || !array_key_exists('label', $item)) {
                        throw new Exception("字典 {$code} 明细格式无效");
                    }
                    $query = Db::table('eb_system_dict_data')
                        ->where('type_id', $typeId)
                        ->where('value', (string)$item['value'])
                        ->whereNull('delete_time');
                    $existingData = $query->find();
                    $data = [
                        'type_id' => $typeId,
                        'label' => (string)$item['label'],
                        'value' => (string)$item['value'],
                        'code' => $code,
                        'sort' => (int)($item['sort'] ?? 0),
                        'status' => (int)($item['status'] ?? 1),
                        'remark' => (string)($item['remark'] ?? ''),
                        'plugin_code' => $pluginCode,
                    ];
                    if ($existingData) {
                        Db::table('eb_system_dict_data')->where('id', (int)$existingData['id'])->update($data);
                    } else {
                        $data['created_by'] = 1;
                        $data['updated_by'] = 1;
                        $data['create_time'] = date('Y-m-d H:i:s');
                        $data['update_time'] = date('Y-m-d H:i:s');
                        $data['delete_time'] = null;
                        Db::table('eb_system_dict_data')->insert($data);
                    }
                }
            }
            $count++;
        }
        return $count;
    }

    private function loadResourceFile(string $file, string $resourceName): array
    {
        if (!is_file($file)) {
            throw new Exception("{$resourceName}资源文件不存在: {$file}");
        }
        $resource = require $file;
        if (!is_array($resource)) {
            throw new Exception("{$resourceName}资源文件必须返回数组");
        }
        return $resource;
    }

    private function log(string $logFile, string $message): void
    {
        file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . "] {$message}\n", FILE_APPEND);
    }

    private function deleteDirectory(string $dir, string $allowedRoot): void
    {
        $root = realpath($allowedRoot);
        $target = realpath($dir);
        if (!$root || !$target || !str_starts_with(strtolower($target . DIRECTORY_SEPARATOR), strtolower($root . DIRECTORY_SEPARATOR))) {
            throw new Exception('插件目录路径校验失败');
        }
        foreach (array_diff(scandir($target), ['.', '..']) as $item) {
            $path = $target . DIRECTORY_SEPARATOR . $item;
            is_dir($path) && !is_link($path) ? $this->deleteDirectory($path, $allowedRoot) : @unlink($path);
        }
        @rmdir($target);
    }

    private function assertPlugin(string $pluginCode): void
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $pluginCode) || in_array($pluginCode, ['saiadmin', 'saicode'], true)) {
            throw new Exception('插件代码无效或不允许卸载');
        }
    }
}
