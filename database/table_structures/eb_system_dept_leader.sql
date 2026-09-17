CREATE TABLE `eb_system_dept_leader` (
  `leader_id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `dept_id` int unsigned NOT NULL COMMENT '部门主键',
  `user_id` int unsigned NOT NULL COMMENT '角色主键',
  PRIMARY KEY (`leader_id`) USING BTREE,
  KEY `idx_dept_id` (`dept_id`) USING BTREE,
  KEY `idx_user_id` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='部门领导关联表';
