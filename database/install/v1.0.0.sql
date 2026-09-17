/*
 Navicat Premium Data Transfer
 default password：asxiqux.j@#4
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for eb_system_config
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_config`;
CREATE TABLE `eb_system_config`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '编号',
  `group_id` int NULL DEFAULT NULL COMMENT '组id',
  `key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置键名',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '配置值',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '配置名称',
  `input_type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '数据输入类型',
  `config_select_data` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '配置选项数据',
  `sort` smallint UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`, `key`) USING BTREE,
  INDEX `group_id`(`group_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '参数配置信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_config
-- ----------------------------
INSERT INTO `eb_system_config` VALUES (1, 1, 'site_copyright', 'Copyright © 2024 saithink', '版权信息', 'textarea', NULL, 96, '');
INSERT INTO `eb_system_config` VALUES (2, 1, 'site_desc', '基于vue3 + webman 的极速开发框架', '网站描述', 'textarea', NULL, 97, NULL);
INSERT INTO `eb_system_config` VALUES (3, 1, 'site_keywords', '后台管理系统', '网站关键字', 'input', NULL, 98, NULL);
INSERT INTO `eb_system_config` VALUES (4, 1, 'site_name', 'SaiAdmin', '网站名称', 'input', NULL, 99, NULL);
INSERT INTO `eb_system_config` VALUES (5, 1, 'site_record_number', '1', '网站备案号', 'input', NULL, 95, NULL);
INSERT INTO `eb_system_config` VALUES (6, 2, 'upload_allow_file', 'png,jpeg,jpg,gif,mp4', '文件类型', 'input', NULL, 0, NULL);
INSERT INTO `eb_system_config` VALUES (7, 2, 'upload_allow_image', 'jpg,jpeg,png,gif,svg,bmp', '图片类型', 'input', NULL, 0, NULL);
INSERT INTO `eb_system_config` VALUES (8, 2, 'upload_mode', '1', '上传模式', 'select', '[{\"label\":\"本地上传\",\"value\":\"1\"},{\"label\":\"阿里云OSS\",\"value\":\"2\"},{\"label\":\"七牛云\",\"value\":\"3\"},{\"label\":\"腾讯云COS\",\"value\":\"4\"},{\"label\":\"亚马逊S3\",\"value\":\"5\"}]', 99, NULL);
INSERT INTO `eb_system_config` VALUES (10, 2, 'upload_size', '20971520', '上传大小', 'input', NULL, 88, '单位Byte,1MB=1024*1024Byte');
INSERT INTO `eb_system_config` VALUES (11, 2, 'local_root', '/public/storage/', '本地存储路径', 'input', NULL, 0, '相对php运行目录');
INSERT INTO `eb_system_config` VALUES (12, 2, 'local_domain', NULL, '本地存储域名', 'input', NULL, 0, 'http://127.0.0.1:8787');
INSERT INTO `eb_system_config` VALUES (13, 2, 'local_uri', '/storage/', '本地访问路径', 'input', NULL, 0, '访问是通过domain + uri');
INSERT INTO `eb_system_config` VALUES (14, 2, 'qiniu_accessKey', NULL, '七牛key', 'input', NULL, 0, '七牛云存储secretId');
INSERT INTO `eb_system_config` VALUES (15, 2, 'qiniu_secretKey', NULL, '七牛secret', 'input', NULL, 0, '七牛云存储secretKey');
INSERT INTO `eb_system_config` VALUES (16, 2, 'qiniu_bucket', NULL, '七牛bucket', 'input', NULL, 0, '七牛云存储bucket');
INSERT INTO `eb_system_config` VALUES (17, 2, 'qiniu_dirname', NULL, '七牛dirname', 'input', NULL, 0, '七牛云存储dirname');
INSERT INTO `eb_system_config` VALUES (18, 2, 'qiniu_domain', NULL, '七牛domain', 'input', NULL, 0, '七牛云存储domain');
INSERT INTO `eb_system_config` VALUES (19, 2, 'cos_secretId', '', '腾讯Id', 'input', NULL, 0, '腾讯云存储secretId');
INSERT INTO `eb_system_config` VALUES (20, 2, 'cos_secretKey', '', '腾讯key', 'input', NULL, 0, '腾讯云secretKey');
INSERT INTO `eb_system_config` VALUES (21, 2, 'cos_bucket', '', '腾讯bucket', 'input', NULL, 0, '腾讯云存储bucket');
INSERT INTO `eb_system_config` VALUES (22, 2, 'cos_dirname', '', '腾讯dirname', 'input', NULL, 0, '腾讯云存储dirname');
INSERT INTO `eb_system_config` VALUES (23, 2, 'cos_domain', '', '腾讯domain', 'input', NULL, 0, '腾讯云存储domain');
INSERT INTO `eb_system_config` VALUES (24, 2, 'cos_region', '', '腾讯region', 'input', NULL, 0, '腾讯云存储region');
INSERT INTO `eb_system_config` VALUES (25, 2, 'oss_accessKeyId', '', '阿里Id', 'input', NULL, 0, '阿里云存储accessKeyId');
INSERT INTO `eb_system_config` VALUES (26, 2, 'oss_accessKeySecret', '', '阿里Secret', 'input', NULL, 0, '阿里云存储accessKeySecret');
INSERT INTO `eb_system_config` VALUES (27, 2, 'oss_bucket', '', '阿里bucket', 'input', NULL, 0, '阿里云存储bucket');
INSERT INTO `eb_system_config` VALUES (28, 2, 'oss_dirname', '', '阿里dirname', 'input', NULL, 0, '阿里云存储dirname');
INSERT INTO `eb_system_config` VALUES (29, 2, 'oss_domain', '', '阿里domain', 'input', NULL, 0, '阿里云存储domain');
INSERT INTO `eb_system_config` VALUES (30, 2, 'oss_endpoint', NULL, '阿里endpoint', 'input', NULL, 0, '阿里云存储endpoint');
INSERT INTO `eb_system_config` VALUES (31, 3, 'Host', NULL, 'SMTP服务器', 'input', '', 100, '');
INSERT INTO `eb_system_config` VALUES (32, 3, 'Port', NULL, 'SMTP端口', 'input', '', 100, '');
INSERT INTO `eb_system_config` VALUES (33, 3, 'Username', NULL, 'SMTP用户名', 'input', '', 100, '');
INSERT INTO `eb_system_config` VALUES (34, 3, 'Password', NULL, 'SMTP密码', 'input', '', 100, '');
INSERT INTO `eb_system_config` VALUES (35, 3, 'SMTPSecure', NULL, 'SMTP验证方式', 'radio', '[\r\n    {\"label\":\"ssl\",\"value\":\"ssl\"},\r\n    {\"label\":\"tsl\",\"value\":\"tsl\"}\r\n]', 100, '');
INSERT INTO `eb_system_config` VALUES (36, 3, 'From', NULL, '默认发件人', 'input', '', 100, '默认发件的邮箱地址');
INSERT INTO `eb_system_config` VALUES (37, 3, 'FromName', NULL, '默认发件名称', 'input', '', 100, '');
INSERT INTO `eb_system_config` VALUES (38, 3, 'CharSet', 'UTF-8', '编码', 'input', '', 100, '');
INSERT INTO `eb_system_config` VALUES (39, 3, 'SMTPDebug', '0', '调试模式', 'radio', '[\r\n    {\"label\":\"关闭\",\"value\":\"0\"},\r\n    {\"label\":\"client\",\"value\":\"1\"},\r\n    {\"label\":\"server\",\"value\":\"2\"}\r\n]', 100, '');
INSERT INTO `eb_system_config` VALUES (40, 2, 's3_key', '', 'key', 'input', '', 0, '');
INSERT INTO `eb_system_config` VALUES (41, 2, 's3_secret', '', 'secret', 'input', '', 0, '');
INSERT INTO `eb_system_config` VALUES (42, 2, 's3_bucket', '', 'bucket', 'input', '', 0, '');
INSERT INTO `eb_system_config` VALUES (43, 2, 's3_dirname', '', 'dirname', 'input', '', 0, '');
INSERT INTO `eb_system_config` VALUES (44, 2, 's3_domain', '', 'domain', 'input', '', 0, '');
INSERT INTO `eb_system_config` VALUES (45, 2, 's3_region', '', 'region', 'input', '', 0, '');
INSERT INTO `eb_system_config` VALUES (46, 2, 's3_version', '', 'version', 'input', '', 0, '');
INSERT INTO `eb_system_config` VALUES (47, 2, 's3_use_path_style_endpoint', '', 'path_style_endpoint', 'input', '', 0, '');
INSERT INTO `eb_system_config` VALUES (48, 2, 's3_endpoint', '', 'endpoint', 'input', '', 0, '');
INSERT INTO `eb_system_config` VALUES (49, 2, 's3_acl', '', 'acl', 'input', '', 0, '');
INSERT INTO `eb_system_config` VALUES (50, 4, 'channel', NULL, '通知渠道', 'select', '[{\"label\":\"钉钉\",\"value\":\"dingtalk\"},{\"label\":\"企业微信\",\"value\":\"wechat\"}]', 100, '');
INSERT INTO `eb_system_config` VALUES (51, 4, 'url', NULL, 'URL地址', 'input', '', 100, '');
INSERT INTO `eb_system_config` VALUES (52, 4, 'sign', '', '签名', 'input', '', 100, '企业微信无需输入');

-- ----------------------------
-- Table structure for eb_system_config_group
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_config_group`;
CREATE TABLE `eb_system_config_group`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字典名称',
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字典标示',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `created_by` int NULL DEFAULT NULL COMMENT '创建人',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新人',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '参数配置分组表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_config_group
-- ----------------------------
INSERT INTO `eb_system_config_group` VALUES (1, '站点配置', 'site_config', NULL, 1, 1, '2021-11-23 10:49:29', '2021-11-23 10:49:29', NULL);
INSERT INTO `eb_system_config_group` VALUES (2, '上传配置', 'upload_config', NULL, 1, 1, '2021-11-23 10:49:29', '2021-11-23 10:49:29', NULL);
INSERT INTO `eb_system_config_group` VALUES (3, '邮件服务', 'email_config', NULL, 1, 1, '2021-11-23 10:49:29', '2021-11-23 10:49:29', NULL);
INSERT INTO `eb_system_config_group` VALUES (4, '通知配置', 'message_config', '', 1, 1, '2025-02-24 01:46:40', '2025-02-24 01:46:40', NULL);

-- ----------------------------
-- Table structure for eb_system_crontab
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_crontab`;
CREATE TABLE `eb_system_crontab`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '任务名称',
  `type` smallint NULL DEFAULT 4 COMMENT '任务类型 (1 command, 2 class, 3 url, 4 eval)',
  `target` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '调用任务字符串',
  `parameter` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '调用任务参数',
  `rule` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '任务执行表达式',
  `singleton` smallint NULL DEFAULT 1 COMMENT '是否单次执行 (1 是 2 不是)',
  `status` smallint NULL DEFAULT 1 COMMENT '状态 (1正常 2停用)',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '定时任务信息表' ROW_FORMAT = DYNAMIC;


-- ----------------------------
-- Table structure for eb_system_crontab_log
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_crontab_log`;
CREATE TABLE `eb_system_crontab_log`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `crontab_id` int UNSIGNED NULL DEFAULT NULL COMMENT '任务ID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '任务名称',
  `target` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '任务调用目标字符串',
  `parameter` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '任务调用参数',
  `exception_info` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '异常信息',
  `status` smallint NULL DEFAULT 1 COMMENT '执行状态 (1成功 2失败)',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '定时任务执行日志表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_crontab_log
-- ----------------------------

-- ----------------------------
-- Table structure for eb_system_dept
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_dept`;
CREATE TABLE `eb_system_dept`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `parent_id` int UNSIGNED NULL DEFAULT NULL COMMENT '父ID',
  `level` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '组级集合',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '部门名称',
  `leader` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '负责人',
  `phone` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '联系电话',
  `status` smallint NULL DEFAULT 1 COMMENT '状态 (1正常 2停用)',
  `sort` smallint UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `parent_id`(`parent_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '部门信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_dept
-- ----------------------------
INSERT INTO `eb_system_dept` VALUES (1, 0, '0', '云管家', '赛弟', '18888888888', 1, 1, '', 1, 1, '2023-10-24 12:00:00', '2026-02-01 04:24:15', NULL);

-- ----------------------------
-- Table structure for eb_system_dept_leader
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_dept_leader`;
CREATE TABLE `eb_system_dept_leader`  (
  `leader_id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '编号',
  `dept_id` int UNSIGNED NOT NULL COMMENT '部门主键',
  `user_id` int UNSIGNED NOT NULL COMMENT '角色主键',
  PRIMARY KEY (`leader_id`) USING BTREE,
  INDEX `idx_dept_id`(`dept_id`) USING BTREE,
  INDEX `idx_user_id`(`user_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '部门领导关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_dept_leader
-- ----------------------------

-- ----------------------------
-- Table structure for eb_system_dict_data
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_dict_data`;
CREATE TABLE `eb_system_dict_data`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type_id` int UNSIGNED NULL DEFAULT NULL COMMENT '字典类型ID',
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字典标签',
  `value` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字典值',
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字典标示',
  `sort` smallint UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `status` smallint NULL DEFAULT 1 COMMENT '状态 (1正常 2停用)',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `type_id`(`type_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '字典数据表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_dict_data
-- ----------------------------
INSERT INTO `eb_system_dict_data` VALUES (3, 2, '本地存储', '1', 'upload_mode', 99, 1, NULL, 1, 1, '2021-06-27 13:33:43', '2021-06-27 13:33:43', NULL);
INSERT INTO `eb_system_dict_data` VALUES (4, 2, '阿里云OSS', '2', 'upload_mode', 98, 1, NULL, 1, 1, '2021-06-27 13:33:55', '2021-06-27 13:33:55', NULL);
INSERT INTO `eb_system_dict_data` VALUES (5, 2, '七牛云', '3', 'upload_mode', 97, 1, NULL, 1, 1, '2021-06-27 13:34:07', '2023-12-13 16:50:26', NULL);
INSERT INTO `eb_system_dict_data` VALUES (6, 2, '腾讯云COS', '4', 'upload_mode', 96, 1, NULL, 1, 1, '2021-06-27 13:34:19', '2023-12-13 16:47:34', NULL);
INSERT INTO `eb_system_dict_data` VALUES (7, 3, '正常', '1', 'data_status', 0, 1, '1为正常', 1, 1, '2021-06-27 13:36:51', '2021-06-27 13:37:01', NULL);
INSERT INTO `eb_system_dict_data` VALUES (8, 3, '停用', '2', 'data_status', 0, 1, '2为停用', 1, 1, '2021-06-27 13:37:10', '2021-06-27 13:37:10', NULL);
INSERT INTO `eb_system_dict_data` VALUES (9, 4, '统计页面', 'statistics', 'dashboard', 0, 1, '管理员用', 1, 1, '2021-08-09 12:53:53', '2023-11-16 11:39:17', NULL);
INSERT INTO `eb_system_dict_data` VALUES (10, 4, '工作台', 'work', 'dashboard', 0, 1, '员工使用', 1, 1, '2021-08-09 12:54:18', '2021-08-09 12:54:18', NULL);
INSERT INTO `eb_system_dict_data` VALUES (11, 5, '男', '1', 'sex', 0, 1, NULL, 1, 1, '2021-08-09 12:55:00', '2021-08-09 12:55:00', NULL);
INSERT INTO `eb_system_dict_data` VALUES (12, 5, '女', '2', 'sex', 0, 1, NULL, 1, 1, '2021-08-09 12:55:08', '2021-08-09 12:55:08', NULL);
INSERT INTO `eb_system_dict_data` VALUES (13, 5, '未知', '3', 'sex', 0, 1, NULL, 1, 1, '2021-08-09 12:55:16', '2021-08-09 12:55:16', NULL);
INSERT INTO `eb_system_dict_data` VALUES (22, 7, '通知', '1', 'backend_notice_type', 2, 1, NULL, 1, 1, '2021-11-11 17:29:27', '2021-11-11 17:30:51', NULL);
INSERT INTO `eb_system_dict_data` VALUES (23, 7, '公告', '2', 'backend_notice_type', 1, 1, NULL, 1, 1, '2021-11-11 17:31:42', '2021-11-11 17:31:42', NULL);
INSERT INTO `eb_system_dict_data` VALUES (39, 6, '通知', 'notice', 'queue_msg_type', 1, 1, NULL, 1, 1, '2021-12-25 18:30:31', '2024-01-20 14:42:55', NULL);
INSERT INTO `eb_system_dict_data` VALUES (40, 6, '公告', 'announcement', 'queue_msg_type', 2, 1, NULL, 1, 1, '2021-12-25 18:31:00', '2024-01-20 14:42:57', NULL);
INSERT INTO `eb_system_dict_data` VALUES (41, 6, '待办', 'todo', 'queue_msg_type', 3, 1, NULL, 1, 1, '2021-12-25 18:31:26', '2024-01-20 14:42:59', NULL);
INSERT INTO `eb_system_dict_data` VALUES (42, 6, '抄送我的', 'carbon_copy_mine', 'queue_msg_type', 4, 1, NULL, 1, 1, '2021-12-25 18:31:26', '2024-01-20 14:42:59', NULL);
INSERT INTO `eb_system_dict_data` VALUES (43, 6, '私信', 'private_message', 'queue_msg_type', 5, 1, NULL, 1, 1, '2021-12-25 18:31:26', '2024-01-20 14:42:59', NULL);
INSERT INTO `eb_system_dict_data` VALUES (44, 12, '图片', 'image', 'attachment_type', 10, 1, NULL, 1, 1, '2022-03-17 14:49:59', '2022-03-17 14:49:59', NULL);
INSERT INTO `eb_system_dict_data` VALUES (45, 12, '文档', 'text', 'attachment_type', 9, 1, NULL, 1, 1, '2022-03-17 14:50:20', '2022-03-17 14:50:49', NULL);
INSERT INTO `eb_system_dict_data` VALUES (46, 12, '音频', 'audio', 'attachment_type', 8, 1, NULL, 1, 1, '2022-03-17 14:50:37', '2022-03-17 14:50:52', NULL);
INSERT INTO `eb_system_dict_data` VALUES (47, 12, '视频', 'video', 'attachment_type', 7, 1, NULL, 1, 1, '2022-03-17 14:50:45', '2022-03-17 14:50:57', NULL);
INSERT INTO `eb_system_dict_data` VALUES (48, 12, '应用程序', 'application', 'attachment_type', 6, 1, NULL, 1, 1, '2022-03-17 14:50:52', '2022-03-17 14:50:59', NULL);
INSERT INTO `eb_system_dict_data` VALUES (49, 13, '菜单', 'M', 'menu_type', 100, 1, '', 1, 1, '2024-07-31 10:34:12', '2024-07-31 10:34:12', NULL);
INSERT INTO `eb_system_dict_data` VALUES (50, 13, '按钮', 'B', 'menu_type', 100, 1, '', 1, 1, '2024-07-31 10:34:20', '2024-07-31 10:34:20', NULL);
INSERT INTO `eb_system_dict_data` VALUES (51, 13, '外链', 'L', 'menu_type', 100, 1, '', 1, 1, '2024-07-31 10:34:27', '2024-07-31 10:34:27', NULL);
INSERT INTO `eb_system_dict_data` VALUES (52, 13, 'iFrame', 'I', 'menu_type', 100, 1, '', 1, 1, '2024-07-31 10:34:51', '2024-07-31 10:34:51', NULL);
INSERT INTO `eb_system_dict_data` VALUES (53, 14, '是', '1', 'yes_or_no', 100, 1, '', 1, 1, '2024-07-31 10:35:17', '2024-07-31 10:35:17', NULL);
INSERT INTO `eb_system_dict_data` VALUES (54, 14, '否', '2', 'yes_or_no', 100, 1, '', 1, 1, '2024-07-31 10:35:22', '2024-07-31 10:35:22', NULL);
INSERT INTO `eb_system_dict_data` VALUES (55, 15, '全部数据权限', '1', 'data_scope', 100, 1, '', 1, 1, '2024-07-31 10:35:22', '2024-07-31 10:35:22', NULL);
INSERT INTO `eb_system_dict_data` VALUES (56, 15, '自定义数据权限', '2', 'data_scope', 100, 1, '', 1, 1, '2024-07-31 10:35:22', '2024-07-31 10:35:22', NULL);
INSERT INTO `eb_system_dict_data` VALUES (57, 15, '本部门数据权限', '3', 'data_scope', 100, 1, '', 1, 1, '2024-07-31 10:35:22', '2024-07-31 10:35:22', NULL);
INSERT INTO `eb_system_dict_data` VALUES (58, 15, '本部门及以下数据权限', '4', 'data_scope', 100, 1, '', 1, 1, '2024-07-31 10:35:22', '2024-07-31 10:35:22', NULL);
INSERT INTO `eb_system_dict_data` VALUES (59, 15, '本人数据权限', '5', 'data_scope', 100, 1, '', 1, 1, '2024-07-31 10:35:22', '2024-07-31 10:35:22', NULL);
INSERT INTO `eb_system_dict_data` VALUES (60, 2, '亚马逊S3', '2', 'upload_mode', 95, 1, NULL, 1, 1, '2021-06-27 13:34:19', '2023-12-13 16:47:34', NULL);
INSERT INTO `eb_system_dict_data` VALUES (61, 16, '微信开放平台', '1', 'platform_type', 100, 1, '微信开放平台', 1, 1, '2024-12-24 19:29:50', '2024-12-24 19:43:32', NULL);
INSERT INTO `eb_system_dict_data` VALUES (62, 16, '抖音开放平台', '2', 'platform_type', 100, 1, '抖音开放平台', 1, 1, '2024-12-24 19:30:02', '2024-12-24 19:43:35', NULL);
INSERT INTO `eb_system_dict_data` VALUES (63, 17, '微信小程序', '1', 'app_type', 100, 1, '', 1, 1, '2024-12-25 22:40:26', '2024-12-26 22:18:06', NULL);
INSERT INTO `eb_system_dict_data` VALUES (64, 17, '微信公众号', '2', 'app_type', 100, 1, '', 1, 1, '2024-12-25 22:40:35', '2024-12-26 22:18:12', NULL);
INSERT INTO `eb_system_dict_data` VALUES (65, 18, '普通小程序', '0', 'mini_service_type', 100, 1, '', 1, 1, '2024-12-26 18:00:53', '2024-12-26 18:07:42', NULL);
INSERT INTO `eb_system_dict_data` VALUES (66, 18, '试用小程序', '12', 'mini_service_type', 100, 1, '', 1, 1, '2024-12-26 18:01:20', '2024-12-26 18:07:46', NULL);
INSERT INTO `eb_system_dict_data` VALUES (67, 18, '小游戏', '4', 'mini_service_type', 100, 1, '', 1, 1, '2024-12-26 18:01:27', '2024-12-26 18:07:57', NULL);
INSERT INTO `eb_system_dict_data` VALUES (68, 18, '小商店', '10', 'mini_service_type', 100, 1, '', 1, 1, '2024-12-26 18:01:34', '2024-12-26 18:08:01', NULL);
INSERT INTO `eb_system_dict_data` VALUES (69, 18, '门店小程序', '2', 'mini_service_type', 100, 1, '', 1, 1, '2024-12-26 18:01:53', '2024-12-26 18:08:06', NULL);
INSERT INTO `eb_system_dict_data` VALUES (73, 19, '未知', '1', 'register_type', 100, 1, '', 1, 1, '2024-12-26 18:04:55', '2024-12-26 18:04:55', NULL);
INSERT INTO `eb_system_dict_data` VALUES (74, 19, '普通方式注册', '2', 'register_type', 100, 1, '', 1, 1, '2024-12-26 18:05:04', '2024-12-26 18:05:04', NULL);
INSERT INTO `eb_system_dict_data` VALUES (75, 19, '通过复用公众号创建小程序api注册', '3', 'register_type', 100, 1, '', 1, 1, '2024-12-26 18:05:12', '2024-12-26 18:05:12', NULL);
INSERT INTO `eb_system_dict_data` VALUES (76, 19, '通过法人扫脸创建企业小程序api注册', '4', 'register_type', 100, 1, '', 1, 1, '2024-12-26 18:05:21', '2024-12-26 18:05:21', NULL);
INSERT INTO `eb_system_dict_data` VALUES (77, 19, '通过创建试用小程序api注册', '13', 'register_type', 100, 1, '', 1, 1, '2024-12-26 18:05:33', '2024-12-26 18:05:33', NULL);
INSERT INTO `eb_system_dict_data` VALUES (78, 18, '门店小程序', '3', 'mini_service_type', 100, 1, '', 1, 1, '2024-12-26 18:08:13', '2024-12-26 18:08:13', NULL);
INSERT INTO `eb_system_dict_data` VALUES (79, 20, '订阅号', '0', 'official_service_type', 100, 1, '', 1, 1, '2024-12-26 18:09:38', '2024-12-26 18:09:38', NULL);
INSERT INTO `eb_system_dict_data` VALUES (80, 20, '由历史老账号升级后的订阅号', '1', 'official_service_type', 100, 1, '', 1, 1, '2024-12-26 18:09:43', '2024-12-26 18:09:43', NULL);
INSERT INTO `eb_system_dict_data` VALUES (81, 20, '服务号', '2', 'official_service_type', 100, 1, '', 1, 1, '2024-12-26 18:09:55', '2024-12-26 18:09:55', NULL);
INSERT INTO `eb_system_dict_data` VALUES (82, 21, '未认证', '-1', 'mini_verify_type', 100, 1, '', 1, 1, '2024-12-26 18:13:54', '2024-12-26 18:13:54', NULL);
INSERT INTO `eb_system_dict_data` VALUES (83, 21, '微信认证', '0', 'mini_verify_type', 100, 1, '', 1, 1, '2024-12-26 18:14:03', '2024-12-26 18:14:03', NULL);
INSERT INTO `eb_system_dict_data` VALUES (84, 22, '未认证', '-1', 'official_verify_type', 100, 1, '', 1, 1, '2024-12-26 18:14:47', '2024-12-26 18:14:47', NULL);
INSERT INTO `eb_system_dict_data` VALUES (85, 22, '微信认证', '0', 'official_verify_type', 100, 1, '', 1, 1, '2024-12-26 18:14:51', '2024-12-26 18:14:51', NULL);
INSERT INTO `eb_system_dict_data` VALUES (86, 22, '新浪微博认证', '1', 'official_verify_type', 100, 1, '', 1, 1, '2024-12-26 18:14:58', '2024-12-26 18:14:58', NULL);
INSERT INTO `eb_system_dict_data` VALUES (87, 22, '已资质认证通过但还未通过名称认证', '3', 'official_verify_type', 100, 1, '', 1, 1, '2024-12-26 18:15:05', '2024-12-26 18:15:05', NULL);
INSERT INTO `eb_system_dict_data` VALUES (88, 22, '已资质认证通过、还未通过名称认证，但通过了新浪微博认证', '4', 'official_verify_type', 100, 1, '', 1, 1, '2024-12-26 18:15:15', '2025-03-01 03:18:16', NULL);
INSERT INTO `eb_system_dict_data` VALUES (89, 23, '正常', '1', 'account_status', 100, 1, '', 1, 1, '2024-12-26 18:16:03', '2024-12-26 18:16:03', NULL);
INSERT INTO `eb_system_dict_data` VALUES (90, 23, '已注销', '14', 'account_status', 100, 1, '', 1, 1, '2024-12-26 18:16:09', '2024-12-26 18:16:09', NULL);
INSERT INTO `eb_system_dict_data` VALUES (91, 23, '已封禁', '16', 'account_status', 100, 1, '', 1, 1, '2024-12-26 18:16:15', '2024-12-26 18:16:15', NULL);
INSERT INTO `eb_system_dict_data` VALUES (92, 23, '已告警', '18', 'account_status', 100, 1, '', 1, 1, '2024-12-26 18:16:23', '2024-12-26 18:16:23', NULL);
INSERT INTO `eb_system_dict_data` VALUES (93, 23, '已告警', '19', 'account_status', 100, 1, '', 1, 1, '2024-12-26 18:16:35', '2024-12-26 18:16:35', NULL);
INSERT INTO `eb_system_dict_data` VALUES (94, 24, '普通方式注册', '0', 'register_type', 100, 1, '', 1, 1, '2024-12-26 18:17:30', '2024-12-26 18:17:30', NULL);
INSERT INTO `eb_system_dict_data` VALUES (95, 24, '通过复用公众号创建小程序api注册', '2', 'register_type', 100, 1, '', 1, 1, '2024-12-26 18:17:35', '2024-12-26 18:17:35', NULL);
INSERT INTO `eb_system_dict_data` VALUES (96, 24, '通过法人扫脸创建企业小程序api注册', '6', 'register_type', 100, 1, '', 1, 1, '2024-12-26 18:17:43', '2024-12-26 18:17:43', NULL);
INSERT INTO `eb_system_dict_data` VALUES (97, 24, '通过创建试用小程序api注册', '13', 'register_type', 100, 1, '', 1, 1, '2024-12-26 18:17:48', '2024-12-26 18:17:48', NULL);
INSERT INTO `eb_system_dict_data` VALUES (98, 24, '通过联盟控制台注册', '15', 'register_type', 100, 1, '', 1, 1, '2024-12-26 18:17:55', '2024-12-26 18:17:55', NULL);
INSERT INTO `eb_system_dict_data` VALUES (99, 24, '通过创建个人小程序api注册', '16', 'register_type', 100, 1, '', 1, 1, '2024-12-26 18:18:03', '2024-12-26 18:18:03', NULL);
INSERT INTO `eb_system_dict_data` VALUES (100, 24, '通过创建个人交易小程序api注册', '17', 'register_type', 100, 1, '', 1, 1, '2024-12-26 18:18:10', '2024-12-26 18:18:10', NULL);
INSERT INTO `eb_system_dict_data` VALUES (101, 24, '通过试用小程序转正api注册', '19', 'register_type', 100, 1, '', 1, 1, '2024-12-26 18:18:17', '2024-12-26 18:18:17', NULL);
INSERT INTO `eb_system_dict_data` VALUES (102, 24, '通过复用商户号创建企业小程序api注册', '22', 'register_type', 100, 1, '', 1, 1, '2024-12-26 18:18:24', '2024-12-26 18:18:24', NULL);
INSERT INTO `eb_system_dict_data` VALUES (103, 24, '通过复用商户号转正api注册', '23', 'register_type', 100, 1, '', 1, 1, '2024-12-26 18:18:36', '2024-12-26 18:18:36', NULL);
INSERT INTO `eb_system_dict_data` VALUES (104, 24, '未知', '-1', 'register_type', 100, 1, '', 1, 1, '2024-12-26 19:44:11', '2024-12-26 19:44:11', NULL);
INSERT INTO `eb_system_dict_data` VALUES (105, 25, '未转发', '0', 'transport_status', 100, 1, '', 1, 1, '2024-12-30 03:12:30', '2024-12-30 03:37:35', NULL);
INSERT INTO `eb_system_dict_data` VALUES (106, 25, '转发成功', '1', 'transport_status', 100, 1, '', 1, 1, '2024-12-30 03:12:38', '2025-03-01 03:06:17', NULL);
INSERT INTO `eb_system_dict_data` VALUES (107, 25, '转发失败', '2', 'transport_status', 100, 1, '', 1, 1, '2024-12-30 03:12:47', '2025-03-01 03:06:22', NULL);
INSERT INTO `eb_system_dict_data` VALUES (109, 26, '等待验证', '0', 'platform_status', 100, 1, '', 1, 1, '2025-01-06 00:04:29', '2025-01-06 00:04:29', NULL);
INSERT INTO `eb_system_dict_data` VALUES (110, 26, '异常', '1', 'platform_status', 100, 1, '', 1, 1, '2025-01-06 00:04:42', '2025-01-06 00:04:42', NULL);
INSERT INTO `eb_system_dict_data` VALUES (111, 26, '正常', '2', 'platform_status', 100, 1, '', 1, 1, '2025-01-06 00:04:50', '2025-01-06 00:04:50', NULL);
INSERT INTO `eb_system_dict_data` VALUES (112, 27, '待申请开通', '1', 'privacy_interface_status', 100, 1, '', 1, 1, '2025-01-27 01:09:43', '2025-01-27 01:09:43', NULL);
INSERT INTO `eb_system_dict_data` VALUES (113, 27, '无权限', '2', 'privacy_interface_status', 100, 1, '', 1, 1, '2025-01-27 01:09:52', '2025-01-27 01:09:52', NULL);
INSERT INTO `eb_system_dict_data` VALUES (114, 27, '申请中', '3', 'privacy_interface_status', 100, 1, '', 1, 1, '2025-01-27 01:09:58', '2025-01-27 01:09:58', NULL);
INSERT INTO `eb_system_dict_data` VALUES (115, 27, '申请失败', '4', 'privacy_interface_status', 100, 1, '', 1, 1, '2025-01-27 01:10:04', '2025-01-27 01:10:04', NULL);
INSERT INTO `eb_system_dict_data` VALUES (116, 27, '已开通', '5', 'privacy_interface_status', 100, 1, '', 1, 1, '2025-01-27 01:10:10', '2025-01-27 01:10:10', NULL);
INSERT INTO `eb_system_dict_data` VALUES (117, 28, '审核中', '1', 'category_audit_status', 100, 1, '', 1, 1, '2025-02-04 01:43:16', '2025-02-04 01:43:16', NULL);
INSERT INTO `eb_system_dict_data` VALUES (118, 28, '审核不通过', '2', 'category_audit_status', 100, 1, '', 1, 1, '2025-02-04 01:43:23', '2025-02-04 01:43:23', NULL);
INSERT INTO `eb_system_dict_data` VALUES (119, 28, '审核通过', '3', 'category_audit_status', 100, 1, '', 1, 1, '2025-02-04 01:43:29', '2025-02-04 01:43:29', NULL);
INSERT INTO `eb_system_dict_data` VALUES (120, 29, 'Webhook', '1', 'message_service', 100, 1, '', 1, 1, '2025-02-19 02:40:36', '2025-02-19 02:40:36', NULL);
INSERT INTO `eb_system_dict_data` VALUES (121, 29, '企业微信', '2', 'message_service', 100, 1, '', 1, 1, '2025-02-19 02:40:50', '2025-02-19 02:40:50', NULL);
INSERT INTO `eb_system_dict_data` VALUES (122, 29, '钉钉', '3', 'message_service', 100, 1, '', 1, 1, '2025-02-19 02:40:57', '2025-02-19 02:40:57', NULL);
INSERT INTO `eb_system_dict_data` VALUES (123, 26, '订购到期', '3', 'platform_status', 100, 1, '', 1, 1, '2025-02-28 04:08:40', '2025-02-28 04:08:40', NULL);
INSERT INTO `eb_system_dict_data` VALUES (124, 30, '审核通过', '0', 'code_audit_status', 100, 1, '', 1, 1, '2026-01-25 02:40:30', '2026-01-25 03:31:23', NULL);
INSERT INTO `eb_system_dict_data` VALUES (125, 30, '审核不通过', '1', 'code_audit_status', 100, 1, '', 1, 1, '2026-01-25 02:40:41', '2026-01-25 02:40:41', NULL);
INSERT INTO `eb_system_dict_data` VALUES (126, 30, '审核中', '2', 'code_audit_status', 100, 1, '', 1, 1, '2026-01-25 02:40:50', '2026-01-25 02:40:50', NULL);
INSERT INTO `eb_system_dict_data` VALUES (127, 30, '已撤回', '3', 'code_audit_status', 100, 1, '', 1, 1, '2026-01-25 02:40:57', '2026-01-25 02:40:57', NULL);
INSERT INTO `eb_system_dict_data` VALUES (128, 30, '审核延后', '4', 'code_audit_status', 100, 1, '', 1, 1, '2026-01-25 02:41:02', '2026-01-25 02:41:02', NULL);
INSERT INTO `eb_system_dict_data` VALUES (129, 30, '无', '5', 'code_audit_status', 100, 1, '', 1, 1, '2026-01-25 02:41:09', '2026-01-25 02:41:09', NULL);

-- ----------------------------
-- Table structure for eb_system_dict_type
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_dict_type`;
CREATE TABLE `eb_system_dict_type`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字典名称',
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字典标示',
  `status` smallint NULL DEFAULT 1 COMMENT '状态 (1正常 2停用)',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '字典类型表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_dict_type
-- ----------------------------
INSERT INTO `eb_system_dict_type` VALUES (2, '存储模式', 'upload_mode', 1, '上传文件存储模式', 1, 1, '2021-06-27 13:33:29', '2021-06-27 13:33:11', NULL);
INSERT INTO `eb_system_dict_type` VALUES (3, '数据状态', 'data_status', 1, '通用数据状态', 1, 1, '2021-06-27 13:33:29', '2021-06-27 13:36:34', NULL);
INSERT INTO `eb_system_dict_type` VALUES (4, '后台首页', 'dashboard', 1, NULL, 1, 1, '2021-06-27 13:33:29', '2023-11-16 11:28:17', NULL);
INSERT INTO `eb_system_dict_type` VALUES (5, '性别', 'sex', 1, NULL, 1, 1, '2021-06-27 13:33:29', '2023-11-16 11:39:12', NULL);
INSERT INTO `eb_system_dict_type` VALUES (6, '消息类型', 'queue_msg_type', 1, NULL, 1, 1, '2021-06-27 13:33:29', '2024-01-20 14:43:06', NULL);
INSERT INTO `eb_system_dict_type` VALUES (7, '后台公告类型', 'backend_notice_type', 1, NULL, 1, 1, '2021-06-27 13:33:29', '2021-11-11 17:29:14', NULL);
INSERT INTO `eb_system_dict_type` VALUES (12, '附件类型', 'attachment_type', 1, NULL, 1, 1, '2021-06-27 13:33:29', '2022-03-17 14:49:23', NULL);
INSERT INTO `eb_system_dict_type` VALUES (13, '菜单类型', 'menu_type', 1, '', 1, 1, '2024-07-31 10:33:37', '2024-07-31 10:33:37', NULL);
INSERT INTO `eb_system_dict_type` VALUES (14, '是否', 'yes_or_no', 1, '', 1, 1, '2024-07-31 10:35:07', '2024-07-31 10:35:07', NULL);
INSERT INTO `eb_system_dict_type` VALUES (15, '数据权限', 'data_scope', 1, '', 1, 1, '2024-07-31 10:35:07', '2024-07-31 10:35:07', NULL);
INSERT INTO `eb_system_dict_type` VALUES (16, '平台类型', 'platform_type', 1, '', 1, 1, '2024-12-24 19:28:54', '2024-12-24 19:28:54', NULL);
INSERT INTO `eb_system_dict_type` VALUES (17, '应用类型', 'app_type', 1, '', 1, 1, '2024-12-25 22:40:01', '2024-12-25 22:40:01', NULL);
INSERT INTO `eb_system_dict_type` VALUES (18, '小程序服务类型', 'mini_service_type', 1, '', 1, 1, '2024-12-26 18:00:45', '2024-12-26 18:07:13', NULL);
INSERT INTO `eb_system_dict_type` VALUES (19, '注册方式', 'register_type', 1, '', 1, 1, '2024-12-26 18:04:35', '2024-12-26 18:08:37', '2024-12-26 18:08:37');
INSERT INTO `eb_system_dict_type` VALUES (20, '公众号服务类型', 'official_service_type', 1, '', 1, 1, '2024-12-26 18:09:25', '2024-12-26 18:09:25', NULL);
INSERT INTO `eb_system_dict_type` VALUES (21, '小程序认证类型', 'mini_verify_type', 1, '', 1, 1, '2024-12-26 18:13:38', '2024-12-26 18:13:38', NULL);
INSERT INTO `eb_system_dict_type` VALUES (22, '公众号认证类型', 'official_verify_type', 1, '', 1, 1, '2024-12-26 18:14:28', '2024-12-26 18:14:28', NULL);
INSERT INTO `eb_system_dict_type` VALUES (23, '账号状态', 'account_status', 1, '', 1, 1, '2024-12-26 18:15:54', '2024-12-26 18:15:54', NULL);
INSERT INTO `eb_system_dict_type` VALUES (24, '注册类型', 'register_type', 1, '', 1, 1, '2024-12-26 18:17:09', '2024-12-26 18:17:15', NULL);
INSERT INTO `eb_system_dict_type` VALUES (25, '转发状态', 'transport_status', 1, '', 1, 1, '2024-12-30 03:12:18', '2024-12-30 03:12:18', NULL);
INSERT INTO `eb_system_dict_type` VALUES (26, '平台状态', 'platform_status', 1, '', 1, 1, '2025-01-05 23:59:55', '2025-01-05 23:59:55', NULL);
INSERT INTO `eb_system_dict_type` VALUES (27, '地理位置接口状态', 'privacy_interface_status', 1, '获取地理位置接口列表接口申请状态', 1, 1, '2025-01-27 01:09:26', '2025-01-27 01:09:26', NULL);
INSERT INTO `eb_system_dict_type` VALUES (28, '类目审核状态', 'category_audit_status', 1, '微信类目审核状态', 1, 1, '2025-02-04 01:42:21', '2025-02-04 01:42:21', NULL);
INSERT INTO `eb_system_dict_type` VALUES (29, '消息通知类型', 'message_service', 1, '', 1, 1, '2025-02-19 02:40:04', '2025-02-19 02:40:04', NULL);
INSERT INTO `eb_system_dict_type` VALUES (30, '代码审核状态', 'code_audit_status', 1, '', 1, 1, '2026-01-25 02:40:10', '2026-01-25 02:40:10', NULL);

-- ----------------------------
-- Table structure for eb_system_login_log
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_login_log`;
CREATE TABLE `eb_system_login_log`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户名',
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '登录IP地址',
  `ip_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'IP所属地',
  `os` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作系统',
  `browser` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '浏览器',
  `status` smallint NULL DEFAULT 1 COMMENT '登录状态 (1成功 2失败)',
  `message` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '提示消息',
  `login_time` datetime NOT NULL COMMENT '登录时间',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '登录日志表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for eb_system_mail
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_mail`;
CREATE TABLE `eb_system_mail`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '编号',
  `gateway` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '网关',
  `from` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '发送人',
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '接收人',
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '验证码',
  `content` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '邮箱内容',
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '发送状态',
  `response` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '返回结果',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '邮件记录' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_mail
-- ----------------------------

-- ----------------------------
-- Table structure for eb_system_menu
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_menu`;
CREATE TABLE `eb_system_menu`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `parent_id` int UNSIGNED NULL DEFAULT NULL COMMENT '父ID',
  `level` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '组级集合',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单名称',
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单标识代码',
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '菜单图标',
  `route` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '路由地址',
  `component` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '组件路径',
  `redirect` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '跳转地址',
  `is_hidden` smallint NULL DEFAULT 1 COMMENT '是否隐藏 (1是 2否)',
  `is_layout` tinyint UNSIGNED NULL DEFAULT 1 COMMENT '继承layout',
  `type` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '菜单类型, (M菜单 B按钮 L链接 I iframe)',
  `generate_id` int NULL DEFAULT 0 COMMENT '生成id',
  `generate_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '生成key',
  `status` smallint NULL DEFAULT 1 COMMENT '状态 (1正常 2停用)',
  `sort` smallint UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '菜单信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_menu
-- ----------------------------
INSERT INTO `eb_system_menu` VALUES (1000, 0, '0', '权限', 'permission', 'bi:person-lock', 'permission', '', NULL, 2, 1, 'M', 0, NULL, 1, 99, NULL, 1, 1, '2021-07-25 18:48:47', '2023-11-14 23:13:42', NULL);
INSERT INTO `eb_system_menu` VALUES (1100, 1000, '0,1000', '用户管理', '/core/user', 'bi:people', 'user', 'system/user/index', NULL, 2, 1, 'M', 0, NULL, 1, 99, NULL, 1, 1, '2021-07-25 18:50:15', '2021-07-25 18:50:15', NULL);
INSERT INTO `eb_system_menu` VALUES (1101, 1100, '0,1000,1100', '用户列表', '/core/user/index', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 18:50:15', '2021-07-25 18:50:15', NULL);
INSERT INTO `eb_system_menu` VALUES (1102, 1100, '0,1000,1100', '用户回收站列表', '/core/user/recycle', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 18:50:15', '2021-07-25 18:50:15', NULL);
INSERT INTO `eb_system_menu` VALUES (1103, 1100, '0,1000,1100', '用户保存', '/core/user/save', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 18:50:15', '2021-07-25 18:50:15', NULL);
INSERT INTO `eb_system_menu` VALUES (1104, 1100, '0,1000,1100', '用户更新', '/core/user/update', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 18:50:15', '2021-07-25 18:50:15', NULL);
INSERT INTO `eb_system_menu` VALUES (1105, 1100, '0,1000,1100', '用户删除', '/core/user/destroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 18:50:15', '2021-07-25 18:50:15', NULL);
INSERT INTO `eb_system_menu` VALUES (1106, 1100, '0,1000,1100', '用户读取', '/core/user/read', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 18:50:15', '2021-07-25 18:50:15', NULL);
INSERT INTO `eb_system_menu` VALUES (1107, 1100, '0,1000,1100', '用户恢复', '/core/user/recovery', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 18:50:15', '2021-07-25 18:50:15', NULL);
INSERT INTO `eb_system_menu` VALUES (1108, 1100, '0,1000,1100', '用户销毁', '/core/user/realDestroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2024-04-30 16:30:00', '2023-04-30 16:30:00', NULL);
INSERT INTO `eb_system_menu` VALUES (1111, 1100, '0,1000,1100', '用户状态改变', '/core/user/changeStatus', '', NULL, '', NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 18:53:02', '2021-07-25 18:53:02', NULL);
INSERT INTO `eb_system_menu` VALUES (1112, 1100, '0,1000,1100', '用户初始化密码', '/core/user/initUserPassword', '', NULL, '', NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 18:55:55', '2021-07-25 18:55:55', NULL);
INSERT INTO `eb_system_menu` VALUES (1113, 1100, '0,1000,1100', '更新用户缓存', '/core/user/cache', '', NULL, '', NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-08-08 18:30:57', '2021-08-08 18:30:57', NULL);
INSERT INTO `eb_system_menu` VALUES (1114, 1100, '0,1000,1100', '设置用户首页', '/core/user/setHomePage', '', NULL, '', NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-08-08 18:34:30', '2021-08-08 18:34:30', NULL);
INSERT INTO `eb_system_menu` VALUES (1200, 1000, '0,1000', '菜单管理', '/core/menu', 'bi:layout-sidebar', 'menu', 'system/menu/index', NULL, 2, 1, 'M', 0, NULL, 1, 96, NULL, 1, 1, '2021-07-25 19:01:47', '2021-07-25 19:01:47', NULL);
INSERT INTO `eb_system_menu` VALUES (1201, 1200, '0,1000,1200', '菜单列表', '/core/menu/index', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:01:47', '2021-07-25 19:01:47', NULL);
INSERT INTO `eb_system_menu` VALUES (1202, 1200, '0,1000,1200', '菜单回收站', '/core/menu/recycle', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:01:47', '2021-07-25 19:01:47', NULL);
INSERT INTO `eb_system_menu` VALUES (1203, 1200, '0,1000,1200', '菜单保存', '/core/menu/save', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:01:47', '2021-07-25 19:01:47', NULL);
INSERT INTO `eb_system_menu` VALUES (1204, 1200, '0,1000,1200', '菜单更新', '/core/menu/update', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:01:47', '2021-07-25 19:01:47', NULL);
INSERT INTO `eb_system_menu` VALUES (1205, 1200, '0,1000,1200', '菜单删除', '/core/menu/destroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:01:47', '2021-07-25 19:01:47', NULL);
INSERT INTO `eb_system_menu` VALUES (1206, 1200, '0,1000,1200', '菜单读取', '/core/menu/read', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:01:47', '2021-07-25 19:01:47', NULL);
INSERT INTO `eb_system_menu` VALUES (1207, 1200, '0,1000,1200', '菜单恢复', '/core/menu/recovery', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:01:47', '2021-07-25 19:01:47', NULL);
INSERT INTO `eb_system_menu` VALUES (1208, 1200, '0,1000,1200', '菜单销毁', '/core/menu/realDestroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2024-04-30 16:30:00', '2023-04-30 16:30:00', NULL);
INSERT INTO `eb_system_menu` VALUES (1300, 1000, '0,1000', '部门管理', '/core/dept', 'bi:diagram-3-fill', 'dept', 'system/dept/index', NULL, 2, 1, 'M', 0, NULL, 1, 97, NULL, 1, 1, '2021-07-25 19:16:33', '2021-07-25 19:16:33', NULL);
INSERT INTO `eb_system_menu` VALUES (1301, 1300, '0,1000,1300', '部门列表', '/core/dept/index', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:16:33', '2021-07-25 19:16:33', NULL);
INSERT INTO `eb_system_menu` VALUES (1302, 1300, '0,1000,1300', '部门回收站', '/core/dept/recycle', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:16:33', '2021-07-25 19:16:33', NULL);
INSERT INTO `eb_system_menu` VALUES (1303, 1300, '0,1000,1300', '部门保存', '/core/dept/save', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:16:33', '2021-07-25 19:16:33', NULL);
INSERT INTO `eb_system_menu` VALUES (1304, 1300, '0,1000,1300', '部门更新', '/core/dept/update', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:16:33', '2021-07-25 19:16:33', NULL);
INSERT INTO `eb_system_menu` VALUES (1305, 1300, '0,1000,1300', '部门删除', '/core/dept/destroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:16:33', '2021-07-25 19:16:33', NULL);
INSERT INTO `eb_system_menu` VALUES (1306, 1300, '0,1000,1300', '部门读取', '/core/dept/read', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:16:33', '2021-07-25 19:16:33', NULL);
INSERT INTO `eb_system_menu` VALUES (1307, 1300, '0,1000,1300', '部门恢复', '/core/dept/recovery', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:16:33', '2021-07-25 19:16:33', NULL);
INSERT INTO `eb_system_menu` VALUES (1308, 1300, '0,1000,1300', '部门销毁', '/core/dept/realDestroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2024-04-30 16:30:00', '2023-04-30 16:30:00', NULL);
INSERT INTO `eb_system_menu` VALUES (1311, 1300, '0,1000,1300', '部门状态改变', '/core/dept/changeStatus', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-11-09 18:26:15', '2021-11-09 18:26:15', NULL);
INSERT INTO `eb_system_menu` VALUES (1400, 1000, '0,1000', '角色管理', '/core/role', 'bi:person-heart', 'role', 'system/role/index', NULL, 2, 1, 'M', 0, NULL, 1, 98, NULL, 1, 1, '2021-07-25 19:17:37', '2021-07-25 19:17:37', NULL);
INSERT INTO `eb_system_menu` VALUES (1401, 1400, '0,1000,1400', '角色列表', '/core/role/index', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:17:37', '2021-07-25 19:17:37', NULL);
INSERT INTO `eb_system_menu` VALUES (1402, 1400, '0,1000,1400', '角色回收站', '/core/role/recycle', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:17:38', '2021-07-25 19:17:38', NULL);
INSERT INTO `eb_system_menu` VALUES (1403, 1400, '0,1000,1400', '角色保存', '/core/role/save', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:17:38', '2021-07-25 19:17:38', NULL);
INSERT INTO `eb_system_menu` VALUES (1404, 1400, '0,1000,1400', '角色更新', '/core/role/update', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:17:38', '2021-07-25 19:17:38', NULL);
INSERT INTO `eb_system_menu` VALUES (1405, 1400, '0,1000,1400', '角色删除', '/core/role/destroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:17:38', '2021-07-25 19:17:38', NULL);
INSERT INTO `eb_system_menu` VALUES (1406, 1400, '0,1000,1400', '角色读取', '/core/role/read', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:17:38', '2021-07-25 19:17:38', NULL);
INSERT INTO `eb_system_menu` VALUES (1407, 1400, '0,1000,1400', '角色恢复', '/core/role/recovery', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 19:17:38', '2021-07-25 19:17:38', NULL);
INSERT INTO `eb_system_menu` VALUES (1408, 1400, '0,1000,1400', '角色销毁', '/core/role/realDestroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2024-04-30 16:30:00', '2023-04-30 16:30:00', NULL);
INSERT INTO `eb_system_menu` VALUES (1411, 1400, '0,1000,1400', '角色状态改变', '/core/role/changeStatus', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-30 11:21:24', '2021-07-30 11:21:24', NULL);
INSERT INTO `eb_system_menu` VALUES (1412, 1400, '0,1000,1400', '更新菜单权限', '/core/role/menuPermission', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-08-09 11:52:33', '2021-08-09 11:52:33', NULL);
INSERT INTO `eb_system_menu` VALUES (1413, 1400, '0,1000,1400', '更新数据权限', '/core/role/dataPermission', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-08-09 11:52:52', '2021-08-09 11:52:52', NULL);
INSERT INTO `eb_system_menu` VALUES (1500, 1000, '0,1000', '岗位管理', '/core/post', 'bi:person-fill-gear', 'post', 'system/post/index', NULL, 2, 1, 'M', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 20:46:38', '2021-07-25 20:46:38', NULL);
INSERT INTO `eb_system_menu` VALUES (1501, 1500, '0,1000,1500', '岗位列表', '/core/post/index', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 20:46:38', '2021-07-25 20:46:38', NULL);
INSERT INTO `eb_system_menu` VALUES (1502, 1500, '0,1000,1500', '岗位回收站', '/core/post/recycle', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 20:46:38', '2021-07-25 20:46:38', NULL);
INSERT INTO `eb_system_menu` VALUES (1503, 1500, '0,1000,1500', '岗位保存', '/core/post/save', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 20:46:38', '2021-07-25 20:46:38', NULL);
INSERT INTO `eb_system_menu` VALUES (1504, 1500, '0,1000,1500', '岗位更新', '/core/post/update', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 20:46:38', '2021-07-25 20:46:38', NULL);
INSERT INTO `eb_system_menu` VALUES (1505, 1500, '0,1000,1500', '岗位删除', '/core/post/destroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 20:46:38', '2021-07-25 20:46:38', NULL);
INSERT INTO `eb_system_menu` VALUES (1506, 1500, '0,1000,1500', '岗位读取', '/core/post/read', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 20:46:38', '2021-07-25 20:46:38', NULL);
INSERT INTO `eb_system_menu` VALUES (1507, 1500, '0,1000,1500', '岗位恢复', '/core/post/recovery', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-25 20:46:38', '2021-07-25 20:46:38', NULL);
INSERT INTO `eb_system_menu` VALUES (1508, 1500, '0,1000,1500', '岗位销毁', '/core/post/realDestroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2024-04-30 16:30:00', '2023-04-30 16:30:00', NULL);
INSERT INTO `eb_system_menu` VALUES (1511, 1500, '0,1000,1500', '岗位状态改变', '/core/post/changeStatus', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-11-09 18:26:15', '2021-11-09 18:26:15', NULL);
INSERT INTO `eb_system_menu` VALUES (1512, 1500, '0,1000,1500', '岗位导入', '/core/post/import', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 17:17:03', '2021-07-31 17:17:03', NULL);
INSERT INTO `eb_system_menu` VALUES (1513, 1500, '0,1000,1500', '岗位导出', '/core/post/export', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 17:17:03', '2021-07-31 17:17:03', NULL);
INSERT INTO `eb_system_menu` VALUES (2000, 0, '0', '数据', 'dataCenter', 'icon-storage', 'dataCenter', NULL, NULL, 2, 1, 'M', 0, NULL, 1, 98, NULL, 1, 1, '2021-07-31 17:17:03', '2021-07-31 17:17:03', NULL);
INSERT INTO `eb_system_menu` VALUES (2100, 2000, '0,2000', '数据字典', '/core/dictType', 'bi:ui-radios', 'dict', 'system/dict/index', NULL, 2, 1, 'M', 0, NULL, 1, 99, NULL, 1, 1, '2021-07-31 18:33:45', '2021-07-31 18:33:45', NULL);
INSERT INTO `eb_system_menu` VALUES (2101, 2100, '0,2000,2100', '数据字典列表', '/core/dictType/index', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:33:45', '2021-07-31 18:33:45', NULL);
INSERT INTO `eb_system_menu` VALUES (2102, 2100, '0,2000,2100', '数据字典回收站', '/core/dictType/recycle', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:33:45', '2021-07-31 18:33:45', NULL);
INSERT INTO `eb_system_menu` VALUES (2103, 2100, '0,2000,2100', '数据字典保存', '/core/dictType/save', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:33:45', '2021-07-31 18:33:45', NULL);
INSERT INTO `eb_system_menu` VALUES (2104, 2100, '0,2000,2100', '数据字典更新', '/core/dictType/update', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:33:45', '2021-07-31 18:33:45', NULL);
INSERT INTO `eb_system_menu` VALUES (2105, 2100, '0,2000,2100', '数据字典删除', '/core/dictType/destroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:33:45', '2021-07-31 18:33:45', NULL);
INSERT INTO `eb_system_menu` VALUES (2106, 2100, '0,2000,2100', '数据字典读取', '/core/dictType/read', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:33:46', '2021-07-31 18:33:46', NULL);
INSERT INTO `eb_system_menu` VALUES (2107, 2100, '0,2000,2100', '数据字典恢复', '/core/dictType/recovery', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:33:46', '2021-07-31 18:33:46', NULL);
INSERT INTO `eb_system_menu` VALUES (2108, 2100, '0,2000,2100', '数据字典销毁', '/core/dictType/realDestroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2024-04-30 16:30:00', '2023-04-30 16:30:00', NULL);
INSERT INTO `eb_system_menu` VALUES (2112, 2100, '0,2000,2100', '字典状态改变', '/core/dictType/changeStatus', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-11-09 18:26:15', '2021-11-09 18:26:15', NULL);
INSERT INTO `eb_system_menu` VALUES (2200, 2000, '0,2000', '附件管理', '/core/attachment', 'bi:window-stack', 'attachment', 'system/attachment/index', NULL, 2, 1, 'M', 0, NULL, 1, 98, NULL, 1, 1, '2021-07-31 18:36:34', '2021-07-31 18:36:34', NULL);
INSERT INTO `eb_system_menu` VALUES (2201, 2200, '0,2000,2200', '附件删除', '/core/attachment/destroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:37:20', '2021-07-31 18:37:20', NULL);
INSERT INTO `eb_system_menu` VALUES (2202, 2200, '0,2000,2200', '附件列表', '/core/attachment/index', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:38:05', '2021-07-31 18:38:05', NULL);
INSERT INTO `eb_system_menu` VALUES (2203, 2200, '0,2000,2200', '附件回收站', '/core/attachment/recycle', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:38:57', '2021-07-31 18:38:57', NULL);
INSERT INTO `eb_system_menu` VALUES (2204, 2200, '0,2000,2200', '附件恢复', '/core/attachment/recovery', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:40:44', '2021-07-31 18:40:44', NULL);
INSERT INTO `eb_system_menu` VALUES (2205, 2200, '0,2000,2200', '附件销毁', '/core/attachment/realDestroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2024-04-30 16:30:00', '2023-04-30 16:30:00', NULL);
INSERT INTO `eb_system_menu` VALUES (2300, 2000, '0,2000', '数据表维护', '/core/dataMaintain', 'bi:database-fill-gear', 'dataMaintain', 'system/dataMaintain/index', NULL, 2, 1, 'M', 0, NULL, 1, 95, NULL, 1, 1, '2021-07-31 18:43:20', '2021-07-31 18:43:20', NULL);
INSERT INTO `eb_system_menu` VALUES (2301, 2300, '0,2000,2300', '数据表列表', '/core/dataMaintain/index', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:44:03', '2021-07-31 18:44:03', NULL);
INSERT INTO `eb_system_menu` VALUES (2302, 2300, '0,2000,2300', '数据表详细', '/core/dataMaintain/detailed', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:45:17', '2021-07-31 18:45:17', NULL);
INSERT INTO `eb_system_menu` VALUES (2303, 2300, '0,2000,2300', '数据表清理碎片', '/core/dataMaintain/fragment', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:46:04', '2021-07-31 18:46:04', NULL);
INSERT INTO `eb_system_menu` VALUES (2304, 2300, '0,2000,2300', '数据表优化', '/core/dataMaintain/optimize', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:46:31', '2021-07-31 18:46:31', NULL);
INSERT INTO `eb_system_menu` VALUES (2700, 2000, '0,2000', '系统公告', '/core/notice', 'bi:megaphone', 'notice', 'system/notice/index', NULL, 2, 1, 'M', 0, NULL, 1, 94, NULL, 1, 1, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL);
INSERT INTO `eb_system_menu` VALUES (2701, 2700, '0,2000,2700', '系统公告列表', '/core/notice/index', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL);
INSERT INTO `eb_system_menu` VALUES (2702, 2700, '0,2000,2700', '系统公告回收站', '/core/notice/recycle', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL);
INSERT INTO `eb_system_menu` VALUES (2703, 2700, '0,2000,2700', '系统公告保存', '/core/notice/save', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL);
INSERT INTO `eb_system_menu` VALUES (2704, 2700, '0,2000,2700', '系统公告更新', '/core/notice/update', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL);
INSERT INTO `eb_system_menu` VALUES (2705, 2700, '0,2000,2700', '系统公告删除', '/core/notice/destroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL);
INSERT INTO `eb_system_menu` VALUES (2706, 2700, '0,2000,2700', '系统公告读取', '/core/notice/read', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL);
INSERT INTO `eb_system_menu` VALUES (2707, 2700, '0,2000,2700', '系统公告恢复', '/core/notice/recovery', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-12-25 18:10:20', '2021-12-25 18:10:20', NULL);
INSERT INTO `eb_system_menu` VALUES (2708, 2700, '0,2000,2700', '系统公告销毁', '/core/notice/realDestroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2024-04-30 16:30:00', '2023-04-30 16:30:00', NULL);
INSERT INTO `eb_system_menu` VALUES (3000, 0, '0', '监控', 'monitor', 'bi:app-indicator', 'monitor', NULL, NULL, 2, 1, 'M', 0, NULL, 1, 97, NULL, 1, 1, '2021-07-31 18:49:24', '2021-07-31 18:49:24', NULL);
INSERT INTO `eb_system_menu` VALUES (3200, 3000, '0,3000', '服务监控', '/core/system/monitor', 'bi:gear-wide-connected', 'server', 'system/monitor/server/index', NULL, 2, 1, 'M', 0, NULL, 1, 99, NULL, 1, 1, '2021-07-31 18:52:46', '2021-07-31 18:52:46', NULL);
INSERT INTO `eb_system_menu` VALUES (3300, 3000, '0,3000', '日志监控', 'logs', 'bi:journal-album', 'logs', NULL, NULL, 2, 1, 'M', 0, NULL, 1, 95, NULL, 1, 1, '2021-07-31 18:54:01', '2021-07-31 18:54:01', NULL);
INSERT INTO `eb_system_menu` VALUES (3400, 3300, '0,3000,3200', '登录日志', '/core/logs/deleteLoginLog', 'bi:person-fill-up', 'loginLog', 'system/logs/loginLog', NULL, 2, 1, 'M', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:54:55', '2021-07-31 18:54:55', NULL);
INSERT INTO `eb_system_menu` VALUES (3401, 3400, '0,3000,3200,3300', '登录日志删除', '/core/logs/deleteOperLog', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:56:43', '2021-07-31 18:56:43', NULL);
INSERT INTO `eb_system_menu` VALUES (3500, 3300, '0,3000,3200', '操作日志', '/core/logs/getOperLogPageList', 'bi:list-ul', 'operLog', 'system/logs/operLog', NULL, 2, 1, 'M', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:55:40', '2021-07-31 18:55:40', NULL);
INSERT INTO `eb_system_menu` VALUES (3501, 3500, '0,3000,3200,3500', '操作日志删除', '/core/logs/deleteOperLog', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:56:19', '2021-07-31 18:56:19', NULL);
INSERT INTO `eb_system_menu` VALUES (3600, 3000, '0,3000', '邮件记录', '/core/email/index', 'bi:envelope-at', 'emailLog', 'system/logs/emailLog', NULL, 2, 1, 'M', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:55:40', '2021-07-31 18:55:40', NULL);
INSERT INTO `eb_system_menu` VALUES (3601, 3600, '0,3000,3600', '邮件记录删除', '/core/email/destroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 18:56:19', '2021-07-31 18:56:19', NULL);
INSERT INTO `eb_system_menu` VALUES (4000, 0, '0', '工具', 'devTools', 'bi:tools', 'devTools', NULL, NULL, 2, 1, 'M', 0, NULL, 1, 96, NULL, 1, 1, '2021-07-31 19:03:32', '2021-07-31 19:03:32', NULL);
INSERT INTO `eb_system_menu` VALUES (4200, 4000, '0,4000', '代码生成器', '/tool/code', 'bi:terminal-plus', 'code', 'setting/code/index', NULL, 2, 1, 'M', 0, NULL, 1, 98, NULL, 1, 1, '2021-07-31 19:36:17', '2021-07-31 19:36:17', NULL);
INSERT INTO `eb_system_menu` VALUES (4400, 4000, '0,4000', '定时任务', '/core/crontab', 'bi:clock', 'crontab', 'setting/crontab/index', NULL, 2, 1, 'M', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 19:47:49', '2021-07-31 19:47:49', NULL);
INSERT INTO `eb_system_menu` VALUES (4401, 4400, '0,4000,4400', '定时任务列表', '/core/crontab/index', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 19:47:49', '2021-07-31 19:47:49', NULL);
INSERT INTO `eb_system_menu` VALUES (4402, 4400, '0,4000,4400', '定时任务保存', '/core/crontab/save', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 19:47:49', '2021-07-31 19:47:49', NULL);
INSERT INTO `eb_system_menu` VALUES (4403, 4400, '0,4000,4400', '定时任务更新', '/core/crontab/update', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 19:47:49', '2021-07-31 19:47:49', NULL);
INSERT INTO `eb_system_menu` VALUES (4404, 4400, '0,4000,4400', '定时任务删除', '/core/crontab/destroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 19:47:49', '2021-07-31 19:47:49', NULL);
INSERT INTO `eb_system_menu` VALUES (4405, 4400, '0,4000,4400', '定时任务读取', '/core/crontab/read', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-07-31 19:47:49', '2021-07-31 19:47:49', NULL);
INSERT INTO `eb_system_menu` VALUES (4408, 4400, '0,4000,4400', '定时任务执行', '/core/crontab/run', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-08-07 23:44:06', '2021-08-07 23:44:06', NULL);
INSERT INTO `eb_system_menu` VALUES (4409, 4400, '0,4000,4400', '定时任务日志删除', '/core/crontab/deleteLog', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-12-06 22:06:12', '2021-12-06 22:06:12', NULL);
INSERT INTO `eb_system_menu` VALUES (4500, 0, '0', '系统设置', '/core/config', 'bi:gear', 'system', 'setting/config/index', NULL, 2, 1, 'M', 0, NULL, 1, 95, '', 1, 1, '2021-07-31 19:58:57', '2026-01-29 02:46:29', NULL);
INSERT INTO `eb_system_menu` VALUES (4502, 4500, '0,4500', '配置列表', '/core/config/index', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-08-10 13:09:18', '2021-08-10 13:09:18', NULL);
INSERT INTO `eb_system_menu` VALUES (4504, 4500, '0,4500', '新增配置 ', '/core/config/save', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-08-10 13:11:56', '2021-08-10 13:11:56', NULL);
INSERT INTO `eb_system_menu` VALUES (4505, 4500, '0,4500', '更新配置', '/core/config/update', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-08-10 13:12:25', '2021-08-10 13:12:25', NULL);
INSERT INTO `eb_system_menu` VALUES (4506, 4500, '0,4500', '删除配置', '/core/config/destroy', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-08-10 13:13:33', '2021-08-10 13:13:33', NULL);
INSERT INTO `eb_system_menu` VALUES (4507, 4500, '0,4500', '清除配置缓存', '/core/config/clearCache', NULL, NULL, NULL, NULL, 2, 1, 'B', 0, NULL, 1, 0, NULL, 1, 1, '2021-08-10 13:13:59', '2021-08-10 13:13:59', NULL);
INSERT INTO `eb_system_menu` VALUES (5082, 0, '0', '应用市场', 'upgrade', 'bi:cloud-arrow-down', 'upgrade', 'system/upgrade/index', NULL, 2, 1, 'M', 0, NULL, 1, 1, '', 1, 1, '2026-01-29 02:45:08', '2026-02-09 04:40:40', NULL);
INSERT INTO `eb_system_menu` VALUES (5083, 0, '0', '批量任务', 'task', 'IconSelectAll', 'task', 'system/task/index', NULL, 2, 1, 'M', 0, NULL, 1, 93, '', 1, 1, '2026-02-09 04:42:20', '2026-02-09 04:42:56', NULL);

-- ----------------------------
-- Table structure for eb_system_notice
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_notice`;
CREATE TABLE `eb_system_notice`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `message_id` int NULL DEFAULT NULL COMMENT '消息ID',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标题',
  `type` smallint NULL DEFAULT NULL COMMENT '公告类型(1通知 2公告)',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '公告内容',
  `click_num` int NULL DEFAULT 0 COMMENT '浏览次数',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `created_by` int NULL DEFAULT NULL COMMENT '创建人',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新人',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `message_id`(`message_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统公告表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_notice
-- ----------------------------
INSERT INTO `eb_system_notice` VALUES (1, NULL, '欢迎使用SaiAdmin', 1, '<p>saiadmin是一款基于vue3 + webman 的极速开发框架，前端开发采用JavaScript，后端采用PHP</p>', 0, NULL, 1, 1, '2024-01-20 15:55:36', '2024-01-20 15:55:36', NULL);

-- ----------------------------
-- Table structure for eb_system_oper_log
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_oper_log`;
CREATE TABLE `eb_system_oper_log`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户名',
  `app` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '应用名称',
  `method` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求方式',
  `router` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求路由',
  `service_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '业务名称',
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '请求IP地址',
  `ip_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'IP所属地',
  `request_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '请求数据',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '操作日志表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for eb_system_post
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_post`;
CREATE TABLE `eb_system_post`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '岗位名称',
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '岗位代码',
  `sort` smallint UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `status` smallint NULL DEFAULT 1 COMMENT '状态 (1正常 2停用)',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '岗位信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_post
-- ----------------------------
INSERT INTO `eb_system_post` VALUES (1, '员工', 'yuangong', 1, 1, NULL, 1, 1, '2023-10-24 12:00:00', '2023-11-15 09:51:12', NULL);

-- ----------------------------
-- Table structure for eb_system_role
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_role`;
CREATE TABLE `eb_system_role`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `parent_id` int UNSIGNED NULL DEFAULT NULL COMMENT '父ID',
  `level` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '组级集合',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '角色名称',
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '角色代码',
  `data_scope` smallint NULL DEFAULT 1 COMMENT '数据范围(1:全部数据权限 2:自定义数据权限 3:本部门数据权限 4:本部门及以下数据权限 5:本人数据权限)',
  `status` smallint NULL DEFAULT 1 COMMENT '状态 (1正常 2停用)',
  `sort` smallint UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_role
-- ----------------------------
INSERT INTO `eb_system_role` VALUES (1, 0, '0', '超级管理员（创始人）', 'superAdmin', 1, 1, 100, '系统内置角色，不可删除', 1, 1, '2023-10-24 12:00:00', '2023-10-24 12:00:00', NULL);
INSERT INTO `eb_system_role` VALUES (2, 1, '0,1', '普通管理员', 'admin', 5, 1, 100, '', 1, 1, '2024-12-24 03:07:34', '2024-12-24 03:07:54', NULL);

-- ----------------------------
-- Table structure for eb_system_role_dept
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_role_dept`;
CREATE TABLE `eb_system_role_dept`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '编号',
  `role_id` int UNSIGNED NOT NULL COMMENT '用户主键',
  `dept_id` int UNSIGNED NOT NULL COMMENT '角色主键',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_role_id`(`role_id`) USING BTREE,
  INDEX `idx_dept_id`(`dept_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色与部门关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_role_dept
-- ----------------------------

-- ----------------------------
-- Table structure for eb_system_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_role_menu`;
CREATE TABLE `eb_system_role_menu`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '编号',
  `role_id` int UNSIGNED NOT NULL COMMENT '角色主键',
  `menu_id` int UNSIGNED NOT NULL COMMENT '菜单主键',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_role_id`(`role_id`) USING BTREE,
  INDEX `idx_menu_id`(`menu_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色与菜单关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_role_menu
-- ----------------------------
INSERT INTO `eb_system_role_menu` VALUES (1, 2, 5082);
INSERT INTO `eb_system_role_menu` VALUES (2, 2, 5083);

-- ----------------------------
-- Table structure for eb_system_uploadfile
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_uploadfile`;
CREATE TABLE `eb_system_uploadfile`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `storage_mode` smallint NULL DEFAULT 1 COMMENT '存储模式 (1 本地 2 阿里云 3 七牛云 4 腾讯云)',
  `origin_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '原文件名',
  `object_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '新文件名',
  `hash` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件hash',
  `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '资源类型',
  `storage_path` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '存储目录',
  `suffix` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件后缀',
  `size_byte` bigint NULL DEFAULT NULL COMMENT '字节数',
  `size_info` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件大小',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'url地址',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `file_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '文件类型(身份证 营业执照..)',
  `parse_result` json NULL COMMENT '解析结果',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `hash`(`hash`) USING BTREE,
  INDEX `storage_path`(`storage_path`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '上传文件信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_uploadfile
-- ----------------------------

-- ----------------------------
-- Table structure for eb_system_user
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_user`;
CREATE TABLE `eb_system_user`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID,主键',
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户名',
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密码',
  `user_type` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '100' COMMENT '用户类型:(100系统用户)',
  `nickname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户昵称',
  `phone` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '手机',
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户邮箱',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户头像',
  `signed` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '个人签名',
  `dashboard` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '后台首页类型',
  `dept_id` int UNSIGNED NULL DEFAULT NULL COMMENT '部门ID',
  `status` smallint NULL DEFAULT 1 COMMENT '状态 (1正常 2停用)',
  `login_ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '最后登陆IP',
  `login_time` datetime NULL DEFAULT NULL COMMENT '最后登陆时间',
  `expire_time` datetime NULL DEFAULT NULL COMMENT '产品过期时间',
  `backend_setting` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '后台设置数据',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE,
  INDEX `dept_id`(`dept_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户信息表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_user: asxiqux.j@#4
-- ----------------------------
INSERT INTO `eb_system_user` VALUES (1, 'yunguanjia', '$2y$10$xSFF8kiO9adOx5dHS9.H4en2CEEu7lgFX.poSctm2ImE6uBIpj7Fy', '100', '祭道之上', '13888888888', 'admin@admin.com', 'https://image.saithink.top/saiadmin/avatar.jpg', 'Today is very good！', 'statistics', 0, 2, '127.0.0.1', '2026-02-01 03:28:37', '2020-02-23 19:58:07', NULL, NULL, 1, 1, '2024-01-20 16:02:23', '2026-02-01 03:28:38', NULL);

-- ----------------------------
-- Table structure for eb_system_user_post
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_user_post`;
CREATE TABLE `eb_system_user_post`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int UNSIGNED NOT NULL COMMENT '用户主键',
  `post_id` int UNSIGNED NOT NULL COMMENT '岗位主键',
  PRIMARY KEY (`id`, `user_id`) USING BTREE,
  INDEX `idx_user_id`(`user_id`) USING BTREE,
  INDEX `idx_post_id`(`post_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户与岗位关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_user_post
-- ----------------------------

-- ----------------------------
-- Table structure for eb_system_user_role
-- ----------------------------
DROP TABLE IF EXISTS `eb_system_user_role`;
CREATE TABLE `eb_system_user_role`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '编号',
  `user_id` int UNSIGNED NOT NULL COMMENT '用户主键',
  `role_id` int UNSIGNED NOT NULL COMMENT '角色主键',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_user_id`(`user_id`) USING BTREE,
  INDEX `idx_role_id`(`role_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户与角色关联表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of eb_system_user_role
-- ----------------------------
INSERT INTO `eb_system_user_role` VALUES (1, 1, 1);

-- ----------------------------
-- Table structure for eb_tool_generate_columns
-- ----------------------------
DROP TABLE IF EXISTS `eb_tool_generate_columns`;
CREATE TABLE `eb_tool_generate_columns`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `table_id` int UNSIGNED NULL DEFAULT NULL COMMENT '所属表ID',
  `column_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段名称',
  `column_comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段注释',
  `column_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段类型',
  `default_value` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '默认值',
  `is_pk` smallint NULL DEFAULT 1 COMMENT '1 非主键 2 主键',
  `is_required` smallint NULL DEFAULT 1 COMMENT '1 非必填 2 必填',
  `is_insert` smallint NULL DEFAULT 1 COMMENT '1 非插入字段 2 插入字段',
  `is_edit` smallint NULL DEFAULT 1 COMMENT '1 非编辑字段 2 编辑字段',
  `is_list` smallint NULL DEFAULT 1 COMMENT '1 非列表显示字段 2 列表显示字段',
  `is_query` smallint NULL DEFAULT 1 COMMENT '1 非查询字段 2 查询字段',
  `is_sort` smallint NULL DEFAULT 1 COMMENT '1 非排序 2 排序',
  `query_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'eq' COMMENT '查询方式 eq 等于, neq 不等于, gt 大于, lt 小于, like 范围',
  `view_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'text' COMMENT '页面控件,text, textarea, password, select, checkbox, radio, date, upload, ma-upload(封装的上传控件)',
  `dict_type` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字典类型',
  `allow_roles` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '允许查看该字段的角色',
  `options` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段其他设置',
  `sort` tinyint UNSIGNED NULL DEFAULT 0 COMMENT '排序',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '代码生成业务字段表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for eb_tool_generate_tables
-- ----------------------------
DROP TABLE IF EXISTS `eb_tool_generate_tables`;
CREATE TABLE `eb_tool_generate_tables`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `table_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表名称',
  `table_comment` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表注释',
  `stub` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'stub类型',
  `template` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '模板名称',
  `namespace` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '命名空间',
  `package_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '控制器包名',
  `business_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '业务名称',
  `class_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '类名称',
  `menu_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '生成菜单名',
  `belong_menu_id` int NULL DEFAULT NULL COMMENT '所属菜单',
  `tpl_category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '生成类型,single 单表CRUD,tree 树表CRUD,parent_sub父子表CRUD',
  `generate_type` smallint NULL DEFAULT 1 COMMENT '1 压缩包下载 2 生成到模块',
  `generate_path` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'saiadmin-vue' COMMENT '前端根目录',
  `generate_model` smallint NULL DEFAULT 1 COMMENT '1 软删除 2 非软删除',
  `generate_scope` smallint NULL DEFAULT 2 COMMENT '1 边界控制 2 边界不控制',
  `generate_menus` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '生成菜单列表',
  `build_menu` smallint NULL DEFAULT 1 COMMENT '是否构建菜单',
  `component_type` smallint NULL DEFAULT 1 COMMENT '组件显示方式',
  `options` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '其他业务选项',
  `form_width` int NULL DEFAULT 600 COMMENT '表单宽度',
  `is_full` tinyint(1) NULL DEFAULT 1 COMMENT '是否全屏',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '数据源',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '代码生成业务表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for saicode_column
-- ----------------------------
DROP TABLE IF EXISTS `saicode_column`;
CREATE TABLE `saicode_column`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `table_id` int UNSIGNED NULL DEFAULT NULL COMMENT '所属表ID',
  `column_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段名称',
  `column_comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段注释',
  `column_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段类型',
  `column_width` int NULL DEFAULT 180 COMMENT '列表宽度',
  `default_value` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '默认值',
  `is_pk` smallint NULL DEFAULT 1 COMMENT '1 非主键 2 主键',
  `is_required` smallint NULL DEFAULT 1 COMMENT '1 非必填 2 必填',
  `is_insert` smallint NULL DEFAULT 1 COMMENT '1 非插入字段 2 插入字段',
  `is_edit` smallint NULL DEFAULT 1 COMMENT '1 非编辑字段 2 编辑字段',
  `is_list` smallint NULL DEFAULT 1 COMMENT '1 非列表显示字段 2 列表显示字段',
  `is_query` smallint NULL DEFAULT 1 COMMENT '1 非查询字段 2 查询字段',
  `is_sort` smallint NULL DEFAULT 1 COMMENT '1 非排序 2 排序',
  `query_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'eq' COMMENT '查询方式',
  `span` smallint NULL DEFAULT NULL COMMENT '布局',
  `view_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'text' COMMENT '页面控件',
  `dict_type` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字典类型',
  `options` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '字段其他设置',
  `list_sort` smallint UNSIGNED NULL DEFAULT 0 COMMENT '列表排序',
  `form_sort` smallint UNSIGNED NULL DEFAULT 0 COMMENT '字段排序',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '代码生成业务字段表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for saicode_table
-- ----------------------------
DROP TABLE IF EXISTS `saicode_table`;
CREATE TABLE `saicode_table`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `table_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表名称',
  `table_comment` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '表注释',
  `stub` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'stub类型',
  `template` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '模板名称',
  `namespace` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '命名空间',
  `package_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '控制器包名',
  `business_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '业务名称',
  `class_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '类名称',
  `menu_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '生成菜单名',
  `belong_menu_id` int NULL DEFAULT NULL COMMENT '所属菜单',
  `tpl_category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '生成类型',
  `generate_type` smallint NULL DEFAULT 1 COMMENT '1 压缩包下载 2 生成到模块',
  `generate_model` smallint NULL DEFAULT 1 COMMENT '1 软删除 2 非软删除',
  `generate_path` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'saiadmin-vue' COMMENT '前端根目录',
  `generate_menus` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '生成菜单列表',
  `component_type` smallint NULL DEFAULT 1 COMMENT '组件方式',
  `form_width` int NULL DEFAULT 600 COMMENT '宽度',
  `is_full` smallint NULL DEFAULT 1 COMMENT '是否全屏',
  `span` smallint NULL DEFAULT NULL COMMENT '布局',
  `options` varchar(1500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '其他业务选项',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '数据源',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '低代码数据表' ROW_FORMAT = DYNAMIC;


-- ----------------------------
-- Table structure for system_batch_task
-- ----------------------------
DROP TABLE IF EXISTS `system_batch_task`;
CREATE TABLE `system_batch_task`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务类型：server_domain/webview_domain/category',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务标题',
  `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '批量参数（JSON格式）',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态：0待处理 1处理中 2完成 3失败',
  `progress` int NOT NULL DEFAULT 0 COMMENT '已完成数量',
  `total` int NOT NULL DEFAULT 0 COMMENT '总数量',
  `success_count` int NOT NULL DEFAULT 0 COMMENT '成功数量',
  `fail_count` int NOT NULL DEFAULT 0 COMMENT '失败数量',
  `result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '任务结果（JSON格式）',
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '错误信息',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '批量任务表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of system_batch_task
-- ----------------------------

-- ----------------------------
-- Table structure for system_batch_task_item
-- ----------------------------
DROP TABLE IF EXISTS `system_batch_task_item`;
CREATE TABLE `system_batch_task_item`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `task_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '批量任务ID',
  `target_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '目标应用ID',
  `target_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '目标应用名称',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态：0待处理 1成功 2失败',
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '处理结果/失败原因',
  `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '处理参数（JSON格式）',
  `created_by` int NULL DEFAULT NULL COMMENT '创建者',
  `updated_by` int NULL DEFAULT NULL COMMENT '更新者',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '批量任务明细表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of system_batch_task_item
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;