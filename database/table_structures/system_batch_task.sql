CREATE TABLE `system_batch_task` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务类型：server_domain/webview_domain/category',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '任务标题',
  `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '批量参数（JSON格式）',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0待处理 1处理中 2完成 3失败',
  `progress` int NOT NULL DEFAULT '0' COMMENT '已完成数量',
  `total` int NOT NULL DEFAULT '0' COMMENT '总数量',
  `success_count` int NOT NULL DEFAULT '0' COMMENT '成功数量',
  `fail_count` int NOT NULL DEFAULT '0' COMMENT '失败数量',
  `result` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '任务结果（JSON格式）',
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '错误信息',
  `created_by` int DEFAULT NULL COMMENT '创建者',
  `updated_by` int DEFAULT NULL COMMENT '更新者',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='批量任务表';
