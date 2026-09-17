-- ----------------------------
-- Table structure for saicode_column
-- ----------------------------
DROP TABLE IF EXISTS `saicode_column`;
CREATE TABLE `saicode_column`  (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `table_id` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '所属表ID',
    `column_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段名称',
    `column_comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段注释',
    `column_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段类型',
    `column_width` int(11) NULL DEFAULT 180 COMMENT '列表宽度',
    `default_value` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '默认值',
    `is_pk` smallint(6) NULL DEFAULT 1 COMMENT '1 非主键 2 主键',
    `is_required` smallint(6) NULL DEFAULT 1 COMMENT '1 非必填 2 必填',
    `is_insert` smallint(6) NULL DEFAULT 1 COMMENT '1 非插入字段 2 插入字段',
    `is_edit` smallint(6) NULL DEFAULT 1 COMMENT '1 非编辑字段 2 编辑字段',
    `is_list` smallint(6) NULL DEFAULT 1 COMMENT '1 非列表显示字段 2 列表显示字段',
    `is_query` smallint(6) NULL DEFAULT 1 COMMENT '1 非查询字段 2 查询字段',
    `is_sort` smallint(6) NULL DEFAULT 1 COMMENT '1 非排序 2 排序',
    `query_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'eq' COMMENT '查询方式',
    `span` smallint(6) NULL DEFAULT NULL COMMENT '布局',
    `view_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'text' COMMENT '页面控件',
    `dict_type` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字典类型',
    `options` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段其他设置',
    `list_sort` smallint(6) UNSIGNED NULL DEFAULT 0 COMMENT '列表排序',
    `form_sort` smallint(6) UNSIGNED NULL DEFAULT 0 COMMENT '字段排序',
    `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
    `created_by` int(11) NULL DEFAULT NULL COMMENT '创建者',
    `updated_by` int(11) NULL DEFAULT NULL COMMENT '更新者',
    `create_time` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
    `update_time` datetime(0) NULL DEFAULT NULL COMMENT '修改时间',
    `delete_time` datetime(0) NULL DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 COMMENT = '代码生成业务字段表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for saicode_table
-- ----------------------------
CREATE TABLE `saicode_table` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
    `table_name` varchar(200) DEFAULT NULL COMMENT '表名称',
    `table_comment` varchar(500) DEFAULT NULL COMMENT '表注释',
    `stub` varchar(50) DEFAULT NULL COMMENT 'stub类型',
    `template` varchar(50) DEFAULT NULL COMMENT '模板名称',
    `namespace` varchar(255) DEFAULT NULL COMMENT '命名空间',
    `package_name` varchar(100) DEFAULT NULL COMMENT '控制器包名',
    `business_name` varchar(50) DEFAULT NULL COMMENT '业务名称',
    `class_name` varchar(50) DEFAULT NULL COMMENT '类名称',
    `menu_name` varchar(100) DEFAULT NULL COMMENT '生成菜单名',
    `belong_menu_id` int(11) DEFAULT NULL COMMENT '所属菜单',
    `tpl_category` varchar(100) DEFAULT NULL COMMENT '生成类型',
    `generate_type` smallint(6) DEFAULT '1' COMMENT '1 压缩包下载 2 生成到模块',
    `generate_model` smallint(6) DEFAULT '1' COMMENT '1 软删除 2 非软删除',
    `generate_path` varchar(100) DEFAULT 'saiadmin-vue' COMMENT '前端根目录',
    `generate_menus` varchar(255) DEFAULT NULL COMMENT '生成菜单列表',
    `component_type` smallint(6) DEFAULT '1' COMMENT '组件方式',
    `form_width` int(11) DEFAULT '600' COMMENT '宽度',
    `is_full` smallint(6) DEFAULT '1' COMMENT '是否全屏',
    `span` smallint(6) DEFAULT NULL COMMENT '布局',
    `options` varchar(1500) DEFAULT NULL COMMENT '其他业务选项',
    `remark` varchar(255) DEFAULT NULL COMMENT '备注',
    `source` varchar(255) DEFAULT NULL COMMENT '数据源',
    `created_by` int(11) DEFAULT NULL COMMENT '创建者',
    `updated_by` int(11) DEFAULT NULL COMMENT '更新者',
    `create_time` datetime DEFAULT NULL COMMENT '创建时间',
    `update_time` datetime DEFAULT NULL COMMENT '修改时间',
    `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 COMMENT='低代码数据表' ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of eb_system_menu
-- ----------------------------
-- ----------------------------
-- Records of eb_system_menu
-- ----------------------------
INSERT INTO `eb_system_menu` VALUES (NULL, 0, '0', 'SAICODE', 'saicode', 'IconCode', 'saicode', NULL, NULL, 2, 1, 'M', 0, NULL, 1, 1, NULL, 1, 1, '2024-07-01 12:00:00', '2024-07-01 12:00:00', NULL);
SET @id := LAST_INSERT_ID();
SET @level := CONCAT('0', ',', @id);
INSERT INTO `eb_system_menu` VALUES (NULL, @id, @level, '代码生成', 'app/saicode/index', 'IconComputer', 'app/saicode/index', 'saicode/index', NULL, 2, 1, 'M', 0, NULL, 1, 0, NULL, 1, 1, '2024-07-01 12:00:00', '2024-07-01 12:00:00', NULL);