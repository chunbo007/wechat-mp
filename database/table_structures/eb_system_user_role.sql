CREATE TABLE `eb_system_user_role` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `user_id` int unsigned NOT NULL COMMENT '用户主键',
  `role_id` int unsigned NOT NULL COMMENT '角色主键',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_user_id` (`user_id`) USING BTREE,
  KEY `idx_role_id` (`role_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='用户与角色关联表';
