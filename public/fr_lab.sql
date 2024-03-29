/*
 Navicat Premium Data Transfer

 Source Server         : DFTC
 Source Server Type    : MariaDB
 Source Server Version : 100337 (10.3.37-MariaDB)
 Source Host           : 192.168.2.100:3307
 Source Schema         : fr_lab

 Target Server Type    : MariaDB
 Target Server Version : 100337 (10.3.37-MariaDB)
 File Encoding         : 65001

 Date: 05/07/2023 15:50:16
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for fr_lab_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `fr_lab_auth_group`;
CREATE TABLE `fr_lab_auth_group`  (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` char(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：为1正常，为0禁用',
  `rules` varchar(600) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id， 多个规则\",\"隔开',
  `update_time` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 63 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of fr_lab_auth_group
-- ----------------------------
INSERT INTO `fr_lab_auth_group` VALUES (1, '系统管理员', 1, '1,46,9,28,29,30,52', 1687776210, 0);
INSERT INTO `fr_lab_auth_group` VALUES (16, '测试1', 1, '1,46', 1687942149, 1533117210);
INSERT INTO `fr_lab_auth_group` VALUES (31, '测试2', 1, '1,46,48,50,3,13,14,15,93,52', 1688464563, 1533803865);
INSERT INTO `fr_lab_auth_group` VALUES (32, '测试3', 1, '1,46', 1688447249, 1533804068);

-- ----------------------------
-- Table structure for fr_lab_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `fr_lab_auth_group_access`;
CREATE TABLE `fr_lab_auth_group_access`  (
  `uid` mediumint(8) UNSIGNED NOT NULL COMMENT '用户id',
  `group_id` mediumint(8) UNSIGNED NOT NULL COMMENT '用户组id',
  `update_time` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  UNIQUE INDEX `uid_group_id`(`uid`, `group_id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `group_id`(`group_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of fr_lab_auth_group_access
-- ----------------------------
INSERT INTO `fr_lab_auth_group_access` VALUES (1, 1, 1687768765, 1687768765);
INSERT INTO `fr_lab_auth_group_access` VALUES (29, 16, 1564551043, 1564551043);
INSERT INTO `fr_lab_auth_group_access` VALUES (31, 32, 1688447239, 1688447239);

-- ----------------------------
-- Table structure for fr_lab_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `fr_lab_auth_rule`;
CREATE TABLE `fr_lab_auth_rule`  (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` char(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规则唯一标识',
  `title` char(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规则中文名称',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '在think_auth_rule 表中定义一条规则时，如果type为1， condition字段就可以定义规则表达式。 如定义{score}>5  and {score}<100  表示用户的分数在5-100之间时这条规则才会通过。',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：为1正常，为0禁用',
  `condition` char(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证',
  `pid` int(8) NOT NULL COMMENT '父级ID',
  `navid` int(8) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `remarks` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `isParent` char(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 124 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of fr_lab_auth_rule
-- ----------------------------
INSERT INTO `fr_lab_auth_rule` VALUES (1, '/', 'root', 1, 1, '', 0, 0, 1688539907, 1533544732, '根节点1', 'true');
INSERT INTO `fr_lab_auth_rule` VALUES (9, 'user', '权限管理', 1, 1, '{status} === 1', 1, 19, 1564554081, 1533544732, '', 'true');
INSERT INTO `fr_lab_auth_rule` VALUES (28, 'user_list', '用户管理', 1, 1, '{status} === 1', 9, 0, 1564553909, 1533544732, '', '');
INSERT INTO `fr_lab_auth_rule` VALUES (29, 'group_list', '角色管理', 1, 1, '{status} === 1', 9, 0, 1564553921, 1533544732, '', '');
INSERT INTO `fr_lab_auth_rule` VALUES (30, 'menu_list', '菜单管理', 1, 1, '{status} === 1', 9, 0, 1564553932, 1533544732, '', '');
INSERT INTO `fr_lab_auth_rule` VALUES (46, 'header_index', '总览', 1, 1, '{status} === 1', 1, 1, 1571887928, 1533544652, '', '');
INSERT INTO `fr_lab_auth_rule` VALUES (52, 'sql', '数据字典', 1, 1, '{status} === 1', 1, 32, 1688538762, 1533613635, '', NULL);

-- ----------------------------
-- Table structure for fr_lab_log
-- ----------------------------
DROP TABLE IF EXISTS `fr_lab_log`;
CREATE TABLE `fr_lab_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户账号',
  `operation` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '动作',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `login_city` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '登录城市',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `userid`(`userid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13506 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for fr_lab_user
-- ----------------------------
DROP TABLE IF EXISTS `fr_lab_user`;
CREATE TABLE `fr_lab_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '姓名',
  `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `password_hash` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码hash',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '邮箱',
  `tel` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '电话',
  `department` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '部门',
  `position` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '职位',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '账户状态',
  `update_time` int(11) NOT NULL COMMENT '最后修改时间',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 207 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of fr_lab_user
-- ----------------------------
INSERT INTO `fr_lab_user` VALUES (1, '系统管理员', 'admin', '$2y$10$VSUsqlHiYLdIruiLBoc5C.ZWGswAV4B2s/m/COPbSChnClmvE5pGG', 'admin@xxx.com', '13500000000', '系统管理员', '-', 1, 1687845221, 1521533170, '');
INSERT INTO `fr_lab_user` VALUES (29, '测试账号1', 'test1', '$2y$10$VSUsqlHiYLdIruiLBoc5C.ZWGswAV4B2s/m/COPbSChnClmvE5pGG', 'test1@xxx.com', '18300000000', '测试组', '-', 1, 1688464090, 1521533170, '');
INSERT INTO `fr_lab_user` VALUES (30, '测试账号2', 'test2', '$2y$10$VSUsqlHiYLdIruiLBoc5C.ZWGswAV4B2s/m/COPbSChnClmvE5pGG', 'test2@xxx.com', '13500000000', '测试组', '-', 0, 1688543245, 1521533170, '');
INSERT INTO `fr_lab_user` VALUES (31, '测试账号3', 'test3', '$2y$10$VSUsqlHiYLdIruiLBoc5C.ZWGswAV4B2s/m/COPbSChnClmvE5pGG', 'test3@xxx.com', '18900000000', '测试组', '-', 1, 1688463991, 1521533170, '');

SET FOREIGN_KEY_CHECKS = 1;
