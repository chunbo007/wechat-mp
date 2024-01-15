-- 授权账号信息
CREATE TABLE `authorizers`
(
    `id`             int(11)             NOT NULL AUTO_INCREMENT,
    `platform_id`    int(11) unsigned    NOT NULL DEFAULT '0' COMMENT 'for platform',
    `appid`          varchar(32)         NOT NULL COMMENT 'appid',
    `app_type`       tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0公众号 1小程序',
    `service_type`   tinyint(1)          NOT NULL DEFAULT '0' COMMENT '0普通小程序 12试用小程序 4小游戏 10小商店 2或者3门店小程序',
    `nick_name`      varchar(32)         NOT NULL DEFAULT '' COMMENT '昵称',
    `user_name`      varchar(32)         NOT NULL DEFAULT '' COMMENT '原始ID',
    `head_img`       varchar(256)        NOT NULL DEFAULT '' COMMENT '头像',
    `qrcode_url`     varchar(256)        NOT NULL DEFAULT '' COMMENT '二维码',
    `principal_name` varchar(64)         NOT NULL DEFAULT '' COMMENT '主体名称',
    `refreshtoken`   varchar(128)        NOT NULL DEFAULT '' COMMENT '刷新token',
    `register_type`  tinyint(1)          NOT NULL DEFAULT '0' COMMENT '-1未知 0普通方式注册 2通过复用公众号创建小程序api注册 6通过法人扫脸创建企业小程序api注册 13通过创建试用小程序api注册 15通过联盟控制台注册 16通过创建个人小程序api注册 17通过创建个人交易小程序api注册 19通过试用小程序转正api注册 22通过复用商户号创建企业小程序api注册 23通过复用商户号转正api注册',
    `account_status` tinyint(1)          NOT NULL DEFAULT '0' COMMENT '1正常 14已注销 16已封禁 18已告警 19已冻结',
    `is_phone`       tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0未绑手机 1已绑手机',
    `is_email`       tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0未绑邮箱 1已绑邮箱',
    `func_info`      text                NOT NULL COMMENT '授权给第三方平台的权限集id列表',
    `verify_info`    int(11)             NOT NULL DEFAULT '-1' COMMENT '-1未认证 0微信认证',
    `auth_time`      int(11)             NOT NULL COMMENT '授权时间',
    `json_data`      text                NOT NULL COMMENT '原始报文',
    `create_time`    int(11)             NOT NULL COMMENT '创建时间',
    `update_time`    int(11)             NOT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE KEY `appid` (`appid`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- 存储ticket、第三方信息等
CREATE TABLE `comm`
(
    `key`        varchar(64) NOT NULL,
    `value`      text        NOT NULL,
    `createtime` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updatetime` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`key`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- 存储平台信息
CREATE TABLE `platform`
(
    `id`               int(10) unsigned                    NOT NULL AUTO_INCREMENT,
    `name`             varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `app_id`           varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `secret`           varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `token`            varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `aes_key`          varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `forward_platform` varchar(50) COLLATE utf8_unicode_ci          DEFAULT NULL COMMENT '转发授权事件：授权事件推送包括：验证票据、授权成功、取消授权、授权更新、快速注册企业小程序、快速注册个人小程序、注册试用小程序、试用小程序快速认证、发起小程序管理员人脸核身、申请小程序备案',
    `forward_app`      varchar(50) COLLATE utf8_unicode_ci          DEFAULT NULL COMMENT '消息与事件推送包括：设置小程序名称、添加类目、提交代码审核。审核结果会向消息与事件接收 URL 进行事件推送',
    `third_secret`     char(32) COLLATE utf8_unicode_ci             DEFAULT NULL COMMENT '外部平台解密数据时的secret',
    `is_default`       tinyint(1) unsigned                 NOT NULL DEFAULT '0',
    `create_time`      int(11) unsigned                    NOT NULL,
    `update_time`      int(11) unsigned                    NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `app_id` (`app_id`) USING BTREE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

-- 用户表
CREATE TABLE `user`
(
    `id`         int(11)     NOT NULL AUTO_INCREMENT,
    `username`   varchar(32) NOT NULL,
    `password`   varchar(64) NOT NULL,
    `createtime` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updatetime` timestamp   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_username_uindex` (`username`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- 推送给消息与事件URL的消息
CREATE TABLE `wxcallback_biz`
(
    `id`          int(10) unsigned NOT NULL AUTO_INCREMENT,
    `appid`       varchar(64)      NOT NULL DEFAULT '',
    `tousername`  varchar(64) NOT NULL DEFAULT '',
    `msgtype`     varchar(64)      NOT NULL DEFAULT '',
    `event`       varchar(64)      NOT NULL DEFAULT '',
    `postbody`    text             NOT NULL,
    `receivetime` int(11)     NOT NULL,
    `create_time` int(11)     NOT NULL,
    `update_time` int(11)     NOT NULL,
    PRIMARY KEY (`id`),
    KEY `receivetime` (`receivetime`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='消息与事件通知回调日志';

-- 推送给授权事件URL的消息
CREATE TABLE `wxcallback_component`
(
    `id`               int(10) unsigned NOT NULL AUTO_INCREMENT,
    `appid`            varchar(32)      NOT NULL COMMENT '第三方平台appid',
    `authorizer_appid` varchar(32)               DEFAULT NULL COMMENT '授权appid',
    `infotype`         varchar(64)      NOT NULL DEFAULT '',
    `postbody`         text             NOT NULL,
    `receivetime`      int(11)          NOT NULL,
    `create_time`      int(11)          NOT NULL,
    `update_time`      int(11)          NOT NULL,
    PRIMARY KEY (`id`),
    KEY `receivetime` (`create_time`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- component_access_token和authorizer_access_token
CREATE TABLE `wxtoken`
(
    `id`         int(10) unsigned NOT NULL AUTO_INCREMENT,
    `type`       int(11)          NOT NULL DEFAULT '0',
    `appid`      varchar(128)     NOT NULL DEFAULT '',
    `token`      text             NOT NULL,
    `expiretime` timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `createtime` timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updatetime` timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `appid_uindex` (`appid`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- 消息转发日志
CREATE TABLE `wxcallback_forward`
(
    `id`          int(10) unsigned NOT NULL AUTO_INCREMENT,
    `appid`       varchar(32)      NOT NULL COMMENT '第三方平台appid',
    `url`         varchar(255)     NOT NULL COMMENT '转发地址',
    `params`      text             NOT NULL COMMENT '转发内容',
    `response`    varchar(255) DEFAULT NULL COMMENT '响应结果',
    `create_time` int(11)          NOT NULL,
    `update_time` int(11)          NOT NULL,
    PRIMARY KEY (`id`),
    KEY `receivetime` (`create_time`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8 COMMENT ='请求转发日志';
