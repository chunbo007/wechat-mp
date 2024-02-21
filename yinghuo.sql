INSERT INTO `yoshop_store_address` (`type`, `name`, `phone`, `province_id`, `city_id`, `region_id`, `detail`, `sort`,
                                    `is_delete`, `store_id`, `create_time`, `update_time`)
VALUES ('20', '仓库', '13500000000', '1', '2', '5', '010号（拒收到付）', '100', '0', 'STOREID', 'TIME', 'TIME');

INSERT INTO `yoshop_user_grade` (`name`, `weight`, `upgrade`, `equity`, `status`, `is_delete`, `store_id`,
                                 `create_time`, `update_time`)
VALUES ('黄金会员', '1', '{\"expend_money\":10000}', '{\"discount\":\"9.8\"}', '1', '0', 'STOREID', 'TIME', 'TIME');
INSERT INTO `yoshop_user_grade` (`name`, `weight`, `upgrade`, `equity`, `status`, `is_delete`, `store_id`,
                                 `create_time`, `update_time`)
VALUES ('铂金会员', '2', '{\"expend_money\":\"20000\"}', '{\"discount\":\"9.7\"}', '1', '0', 'STOREID', 'TIME', 'TIME');
INSERT INTO `yoshop_user_grade` (`name`, `weight`, `upgrade`, `equity`, `status`, `is_delete`, `store_id`,
                                 `create_time`, `update_time`)
VALUES ('钻石会员', '3', '{\"expend_money\":\"30000\"}', '{\"discount\":\"9.6\"}', '1', '0', 'STOREID', 'TIME', 'TIME');


INSERT INTO `yoshop_express` (`express_name`, `kuaidi100_code`, `kdniao_code`, `sort`, `store_id`, `create_time`,
                              `update_time`)
VALUES ('顺丰速运', 'shunfeng', 'SF', '100', 'STOREID', 'TIME', 'TIME');
INSERT INTO `yoshop_express` (`express_name`, `kuaidi100_code`, `kdniao_code`, `sort`, `store_id`, `create_time`,
                              `update_time`)
VALUES ('邮政国内', 'yzguonei', 'YZPY', '100', 'STOREID', 'TIME', 'TIME');
INSERT INTO `yoshop_express` (`express_name`, `kuaidi100_code`, `kdniao_code`, `sort`, `store_id`, `create_time`,
                              `update_time`)
VALUES ('圆通速递', 'yuantong', 'YTO', '100', 'STOREID', 'TIME', 'TIME');
INSERT INTO `yoshop_express` (`express_name`, `kuaidi100_code`, `kdniao_code`, `sort`, `store_id`, `create_time`,
                              `update_time`)
VALUES ('申通快递', 'shentong', 'STO', '100', 'STOREID', 'TIME', 'TIME');
INSERT INTO `yoshop_express` (`express_name`, `kuaidi100_code`, `kdniao_code`, `sort`, `store_id`, `create_time`,
                              `update_time`)
VALUES ('韵达快递', 'yunda', 'YD', '100', 'STOREID', 'TIME', 'TIME');
INSERT INTO `yoshop_express` (`express_name`, `kuaidi100_code`, `kdniao_code`, `sort`, `store_id`, `create_time`,
                              `update_time`)
VALUES ('百世快递', 'huitongkuaidi', 'HTKY', '100', 'STOREID', 'TIME', 'TIME');
INSERT INTO `yoshop_express` (`express_name`, `kuaidi100_code`, `kdniao_code`, `sort`, `store_id`, `create_time`,
                              `update_time`)
VALUES ('中通快递', 'zhongtong', 'ZTO', '100', 'STOREID', 'TIME', 'TIME');
INSERT INTO `yoshop_express` (`express_name`, `kuaidi100_code`, `kdniao_code`, `sort`, `store_id`, `create_time`,
                              `update_time`)
VALUES ('宅急送', 'zhaijisong', 'ZJS', '100', 'STOREID', 'TIME', 'TIME');
INSERT INTO `yoshop_express` (`express_name`, `kuaidi100_code`, `kdniao_code`, `sort`, `store_id`, `create_time`,
                              `update_time`)
VALUES ('极兔速递', 'jtexpress', 'JTSD', '100', 'STOREID', 'TIME', 'TIME');

INSERT INTO `yoshop_goods_service` (`name`, `summary`, `is_default`, `status`, `sort`, `is_delete`, `store_id`,
                                    `create_time`, `update_time`)
VALUES ('七天无理由退货', '满足相应条件时，消费者可申请7天无理由退货', '1', '1', '100', '0', 'STOREID', 'TIME', 'TIME');
INSERT INTO `yoshop_goods_service` (`name`, `summary`, `is_default`, `status`, `sort`, `is_delete`, `store_id`,
                                    `create_time`, `update_time`)
VALUES ('全场包邮', '所有商品包邮（偏远地区除外）', '0', '1', '100', '0', 'STOREID', 'TIME', 'TIME');
INSERT INTO `yoshop_goods_service` (`name`, `summary`, `is_default`, `status`, `sort`, `is_delete`, `store_id`,
                                    `create_time`, `update_time`)
VALUES ('48小时发货', '下单后48小时之内发货', '1', '1', '100', '0', 'STOREID', 'TIME', 'TIME');

INSERT INTO `yoshop_help` (`title`, `content`, `sort`, `is_delete`, `store_id`, `create_time`, `update_time`)
VALUES ('关于小程序', '小程序本身无需下载，无需注册，不占用手机内存，可以跨平台使用，响应迅速，体验接近原生APP。', '99', '0',
        'STOREID', 'TIME', 'TIME');

INSERT INTO `yoshop_delivery` (`name`, `method`, `sort`, `is_delete`, `store_id`, `create_time`, `update_time`)
VALUES ('全国包邮（除偏远地区）', '10', '100', '0', 'STOREID', 'TIME', 'TIME');
