CREATE TABLE `eb_system_notice` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `message_id` int DEFAULT NULL COMMENT '消息ID',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `type` smallint DEFAULT NULL COMMENT '公告类型(1通知 2公告)',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '公告内容',
  `click_num` int DEFAULT '0' COMMENT '浏览次数',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '备注',
  `created_by` int DEFAULT NULL COMMENT '创建人',
  `updated_by` int DEFAULT NULL COMMENT '更新人',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `message_id` (`message_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='系统公告表';
