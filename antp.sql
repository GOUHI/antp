/*
 Navicat Premium Data Transfer

 Source Server         : 本地MxSrvs环境
 Source Server Type    : MySQL
 Source Server Version : 50719
 Source Host           : localhost:3306
 Source Schema         : antp

 Target Server Type    : MySQL
 Target Server Version : 50719
 File Encoding         : 65001

 Date: 28/05/2021 16:00:41
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for master_admin
-- ----------------------------
DROP TABLE IF EXISTS `master_admin`;
CREATE TABLE `master_admin` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(60) NOT NULL COMMENT '账户',
  `username` varchar(60) NOT NULL COMMENT '昵称',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `type` tinyint(4) NOT NULL COMMENT '账户类型\n1、',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '账户状态\n1、正常\n2、黑名单',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `deleete_time` int(11) DEFAULT '0',
  `group_id` bigint(20) NOT NULL COMMENT '所属分组',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';

-- ----------------------------
-- Records of master_admin
-- ----------------------------
BEGIN;
INSERT INTO `master_admin` VALUES (1, 'admin', '超级管理员', '123456', 1, 1, 1622173332, 1622173332, 0, 1);
INSERT INTO `master_admin` VALUES (2, 'test', '测试员', '123456', 1, 1, 1622173332, 1622173332, 0, 1);
COMMIT;

-- ----------------------------
-- Table structure for master_admin_auth
-- ----------------------------
DROP TABLE IF EXISTS `master_admin_auth`;
CREATE TABLE `master_admin_auth` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL COMMENT '权限名称',
  `parent` bigint(20) NOT NULL DEFAULT '0' COMMENT '父级',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='管理员权限表';

-- ----------------------------
-- Records of master_admin_auth
-- ----------------------------
BEGIN;
INSERT INTO `master_admin_auth` VALUES (1, '控制台', 0);
INSERT INTO `master_admin_auth` VALUES (2, '系统设置', 0);
INSERT INTO `master_admin_auth` VALUES (3, '管理员设置', 2);
COMMIT;

-- ----------------------------
-- Table structure for master_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `master_admin_group`;
CREATE TABLE `master_admin_group` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL COMMENT '分组名称',
  `auth_id` bigint(20) NOT NULL COMMENT '分组权限',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `delete_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='管理员分组表';

-- ----------------------------
-- Records of master_admin_group
-- ----------------------------
BEGIN;
INSERT INTO `master_admin_group` VALUES (1, '超级管理员', 1, 1622173332, 1622173332, 0);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
