SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `easyX` DEFAULT CHARACTER SET utf8;

/*用户核心——用户信息表*/
DROP TABLE IF EXISTS `easy_userinfo`;
CREATE TABLE IF NOT EXISTS `easy_userinfo` (
  `uid`             bigint(20)     unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username`        varchar(32)             NOT NULL DEFAULT ''     COMMENT '用户名',
  `password`        varchar(32)             NOT NULL DEFAULT ''     COMMENT '密码',
  `portrait`        varchar(32)             NOT NULL DEFAULT ''     COMMENT '头像',
  `reg_time`        int(10)        unsigned NOT NULL DEFAULT '0'    COMMENT '注册时间',
  `reg_ip`          varchar(32)             NOT NULL DEFAULT ''     COMMENT '注册IP',
  `last_login_time` int(10)        unsigned NOT NULL DEFAULT '0'    COMMENT '最后登录时间',
  `last_login_ip`   varchar(32)             NOT NULL DEFAULT ''     COMMENT '最后登录IP',
  `update_time`     int(10)        unsigned NOT NULL DEFAULT '0'    COMMENT '更新时间',
  `status`          tinyint(4)     unsigned NOT NULL DEFAULT '0'    COMMENT '用户状态',
  `ext_data`        varchar(1000)           NOT NULL DEFAULT '[]'   COMMENT '扩展数据',
  PRIMARY KEY (`uid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='用户信息表' AUTO_INCREMENT=1;

/*用户核心——用户token表*/
DROP TABLE IF EXISTS `easy_user_token`;
CREATE TABLE IF NOT EXISTS `easy_user_token` (
  `id`              int(10)        unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid`             bigint(20)     unsigned NOT NULL DEFAULT '0'    COMMENT '用户ID',
  `token`           varchar(40)             NOT NULL DEFAULT ''     COMMENT 'token',
  `expire_time`     int(10)        unsigned NOT NULL DEFAULT '0'    COMMENT '过期时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY idx_uid(`uid`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='用户Token表' AUTO_INCREMENT=1;
