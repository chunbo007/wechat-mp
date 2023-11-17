-- 授权账号信息
CREATE TABLE `authorizers`
(
    `id`            int(11)      NOT NULL AUTO_INCREMENT,
    `appid`         varchar(32)  NOT NULL,
    `apptype`       int(11)      NOT NULL DEFAULT '0',
    `servicetype`   int(11)      NOT NULL DEFAULT '0',
    `nickname`      varchar(32)  NOT NULL DEFAULT '',
    `username`      varchar(32)  NOT NULL DEFAULT '',
    `headimg`       varchar(256) NOT NULL DEFAULT '',
    `qrcodeurl`     varchar(256) NOT NULL DEFAULT '',
    `principalname` varchar(64)  NOT NULL DEFAULT '',
    `refreshtoken`  varchar(128) NOT NULL DEFAULT '',
    `funcinfo`      text         NOT NULL,
    `verifyinfo`    int(11)      NOT NULL DEFAULT '-1',
    `authtime`      timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatetime`    timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
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
  AUTO_INCREMENT = 2
  DEFAULT CHARSET = utf8;

-- 推送给消息与事件URL的消息
CREATE TABLE `wxcallback_biz`
(
    `id`          int(10) unsigned NOT NULL AUTO_INCREMENT,
    `receivetime` timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `createtime`  timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `tousername`  varchar(64)      NOT NULL DEFAULT '',
    `appid`       varchar(64)      NOT NULL DEFAULT '',
    `msgtype`     varchar(64)      NOT NULL DEFAULT '',
    `event`       varchar(64)      NOT NULL DEFAULT '',
    `postbody`    text             NOT NULL,
    PRIMARY KEY (`id`),
    KEY `receivetime` (`receivetime`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- 推送给授权事件URL的消息
CREATE TABLE `wxcallback_component`
(
    `id`          int(10) unsigned NOT NULL AUTO_INCREMENT,
    `receivetime` timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `createtime`  timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `infotype`    varchar(64)      NOT NULL DEFAULT '',
    `postbody`    text             NOT NULL,
    PRIMARY KEY (`id`),
    KEY `receivetime` (`receivetime`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
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