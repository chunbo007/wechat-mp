CREATE TABLE `eb_system_role_dept` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `role_id` int unsigned NOT NULL COMMENT '用户主键',
  `dept_id` int unsigned NOT NULL COMMENT '角色主键',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_role_id` (`role_id`) USING BTREE,
  KEY `idx_dept_id` (`dept_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='角色与部门关联表';
