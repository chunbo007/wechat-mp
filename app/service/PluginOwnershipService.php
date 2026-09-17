<?php

namespace app\service;

use Exception;
use support\think\Db;

class PluginOwnershipService
{
    private const TABLES = [
        'eb_system_menu',
        'eb_system_crontab',
        'eb_system_dict_type',
        'eb_system_dict_data',
    ];

    private const DICT_CODES = [
        'wechat' => [
            'register_status',
            'complaint_order_type',
            'complaint_order_status',
            'complaint_order_appeal_state',
            'complaint_item_item_type',
            'complaint_item_appeal_item_type',
            'complaint_return_bill_order_status',
        ],
        'wechat_free' => ['wechat_free_register_status'],
    ];

    public function ensureSchema(): void
    {
        foreach (self::TABLES as $table) {
            if (!$this->tableExists($table)) {
                continue;
            }
            if (!$this->columnExists($table, 'plugin_code')) {
                Db::execute("ALTER TABLE `{$table}` ADD COLUMN `plugin_code` varchar(64) NULL DEFAULT NULL COMMENT '所属插件代码，NULL 为框架或共享数据' AFTER `delete_time`");
            }
            if (!$this->indexExists($table, 'idx_plugin_code')) {
                Db::execute("ALTER TABLE `{$table}` ADD INDEX `idx_plugin_code` (`plugin_code`)");
            }
        }
    }

    public function assign(string $pluginCode, array $dictCodes = []): void
    {
        $this->assertPluginCode($pluginCode);
        $this->ensureSchema();

        $escaped = addslashes($pluginCode);
        Db::execute("UPDATE `eb_system_menu` SET `plugin_code` = '{$escaped}' WHERE `plugin_code` IS NULL AND (`code` = '{$escaped}' OR `code` LIKE '{$escaped}/%' OR `code` LIKE '/{$escaped}/%')");
        Db::execute("UPDATE `eb_system_crontab` SET `plugin_code` = '{$escaped}' WHERE `plugin_code` IS NULL AND `target` LIKE '%plugin\\\\{$escaped}\\\\%'");

        $codes = $dictCodes ?: (self::DICT_CODES[$pluginCode] ?? []);
        if ($codes) {
            $codes = array_values(array_unique(array_filter(array_map('strval', $codes))));
            $quoted = implode(', ', array_map(static fn(string $code): string => "'" . addslashes($code) . "'", $codes));
            Db::execute("UPDATE `eb_system_dict_type` SET `plugin_code` = '{$escaped}' WHERE (`plugin_code` IS NULL OR `plugin_code` = '{$escaped}') AND `code` IN ({$quoted})");
            Db::execute("UPDATE `eb_system_dict_data` AS `data` INNER JOIN `eb_system_dict_type` AS `type` ON `type`.`id` = `data`.`type_id` SET `data`.`plugin_code` = '{$escaped}' WHERE `type`.`plugin_code` = '{$escaped}' AND (`data`.`plugin_code` IS NULL OR `data`.`plugin_code` = '{$escaped}')");
        }
    }

    public function preview(string $pluginCode): array
    {
        $this->assertPluginCode($pluginCode);
        $this->ensureSchema();

        $menuIds = Db::table('eb_system_menu')->where('plugin_code', $pluginCode)->column('id');
        return [
            'plugin_code' => $pluginCode,
            'menus' => count($menuIds),
            'role_menus' => $menuIds ? Db::table('eb_system_role_menu')->whereIn('menu_id', $menuIds)->count() : 0,
            'crontabs' => Db::table('eb_system_crontab')->where('plugin_code', $pluginCode)->count(),
            'dict_types' => Db::table('eb_system_dict_type')->where('plugin_code', $pluginCode)->count(),
            'dict_data' => Db::table('eb_system_dict_data')->where('plugin_code', $pluginCode)->count(),
            'business_tables' => $this->pluginTables($pluginCode),
        ];
    }

    public function validateRemoved(string $pluginCode): void
    {
        $preview = $this->preview($pluginCode);
        foreach (['menus', 'role_menus', 'crontabs', 'dict_types', 'dict_data'] as $key) {
            if (!empty($preview[$key])) {
                throw new Exception("插件 {$pluginCode} 卸载后仍残留 {$key} 数据");
            }
        }
        if ($preview['business_tables']) {
            throw new Exception("插件 {$pluginCode} 卸载后仍残留业务表: " . implode(', ', $preview['business_tables']));
        }
    }

    public function validateDictionaryOwnership(string $pluginCode): void
    {
        $this->assertPluginCode($pluginCode);
        $rows = Db::query(
            'SELECT data.id FROM eb_system_dict_data AS data '
            . 'INNER JOIN eb_system_dict_type AS type ON type.id = data.type_id '
            . 'WHERE data.plugin_code = ? AND COALESCE(type.plugin_code, \'\') <> ? LIMIT 1',
            [$pluginCode, $pluginCode]
        );
        if ($rows) {
            throw new Exception("插件 {$pluginCode} 的字典数据归属与字典类型不一致");
        }
    }

    public function pluginTables(string $pluginCode): array
    {
        $this->assertPluginCode($pluginCode);
        $rows = Db::query("SHOW TABLES LIKE '" . addslashes($pluginCode . '\_%') . "'");
        return array_values(array_filter(array_map(
            static fn(array $row): string => (string)array_values($row)[0],
            $rows
        ), static fn(string $table): bool => str_starts_with($table, $pluginCode . '_')));
    }

    private function assertPluginCode(string $pluginCode): void
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $pluginCode)) {
            throw new Exception('插件代码无效');
        }
    }

    private function tableExists(string $table): bool
    {
        return !empty(Db::query("SHOW TABLES LIKE '" . addslashes($table) . "'"));
    }

    private function columnExists(string $table, string $column): bool
    {
        return !empty(Db::query("SHOW COLUMNS FROM `{$table}` LIKE '" . addslashes($column) . "'"));
    }

    private function indexExists(string $table, string $index): bool
    {
        return !empty(Db::query("SHOW INDEX FROM `{$table}` WHERE Key_name = '" . addslashes($index) . "'"));
    }
}
