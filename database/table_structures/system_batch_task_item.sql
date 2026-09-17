CREATE TABLE `system_batch_task_item` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `task_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '批量任务ID',
  `target_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '目标应用ID',
  `target_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '目标应用名称',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0待处理 1成功 2失败',
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '处理结果/失败原因',
  `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '处理参数（JSON格式）',
  `created_by` int DEFAULT NULL COMMENT '创建者',
  `updated_by` int DEFAULT NULL COMMENT '更新者',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='批量任务明细表';
