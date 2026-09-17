-- 插件公共数据归属字段。
ALTER TABLE `eb_system_menu`
    ADD COLUMN `plugin_code` varchar(64) NULL DEFAULT NULL COMMENT '所属插件代码，NULL 为框架或共享数据' AFTER `delete_time`,
    ADD INDEX `idx_plugin_code` (`plugin_code`);

ALTER TABLE `eb_system_crontab`
    ADD COLUMN `plugin_code` varchar(64) NULL DEFAULT NULL COMMENT '所属插件代码，NULL 为框架或共享数据' AFTER `delete_time`,
    ADD INDEX `idx_plugin_code` (`plugin_code`);

ALTER TABLE `eb_system_dict_type`
    ADD COLUMN `plugin_code` varchar(64) NULL DEFAULT NULL COMMENT '所属插件代码，NULL 为框架或共享数据' AFTER `delete_time`,
    ADD INDEX `idx_plugin_code` (`plugin_code`);

ALTER TABLE `eb_system_dict_data`
    ADD COLUMN `plugin_code` varchar(64) NULL DEFAULT NULL COMMENT '所属插件代码，NULL 为框架或共享数据' AFTER `delete_time`,
    ADD INDEX `idx_plugin_code` (`plugin_code`);

-- 历史菜单与计划任务回填。只匹配明确的插件标识，不处理无法可靠判断的数据。
UPDATE `eb_system_menu`
SET `plugin_code` = 'wechat'
WHERE `plugin_code` IS NULL
  AND (`code` = 'wechat' OR `code` LIKE 'wechat/%' OR `code` LIKE '/wechat/%');

UPDATE `eb_system_menu`
SET `plugin_code` = 'wechat_free'
WHERE `plugin_code` IS NULL
  AND (`code` = 'wechat_free' OR `code` LIKE 'wechat_free/%' OR `code` LIKE '/wechat_free/%');

UPDATE `eb_system_crontab`
SET `plugin_code` = 'wechat'
WHERE `plugin_code` IS NULL AND `target` LIKE '%plugin\\wechat\\%';

UPDATE `eb_system_crontab`
SET `plugin_code` = 'wechat_free'
WHERE `plugin_code` IS NULL AND `target` LIKE '%plugin\\wechat_free\\%';

-- register_status 同时出现在两版插件中；企业版已安装时归企业版，否则归开源版。
UPDATE `eb_system_dict_type`
SET `plugin_code` = CASE
    WHEN EXISTS (SELECT 1 FROM `eb_system_menu` WHERE `plugin_code` = 'wechat') THEN 'wechat'
    ELSE 'wechat_free'
END
WHERE `plugin_code` IS NULL AND `code` = 'register_status';

UPDATE `eb_system_dict_type`
SET `plugin_code` = 'wechat'
WHERE `plugin_code` IS NULL AND `code` IN (
    'complaint_order_type',
    'complaint_order_status',
    'complaint_order_appeal_state',
    'complaint_item_item_type',
    'complaint_item_appeal_item_type',
    'complaint_return_bill_order_status'
);

UPDATE `eb_system_dict_data` AS `data`
INNER JOIN `eb_system_dict_type` AS `type` ON `type`.`id` = `data`.`type_id`
SET `data`.`plugin_code` = `type`.`plugin_code`
WHERE `data`.`plugin_code` IS NULL AND `type`.`plugin_code` IS NOT NULL;
