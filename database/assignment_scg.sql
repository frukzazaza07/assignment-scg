/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 100424
 Source Host           : localhost:3306
 Source Schema         : assignment_scg

 Target Server Type    : MySQL
 Target Server Version : 100424
 File Encoding         : 65001

 Date: 23/10/2023 22:53:08
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for google_map_log
-- ----------------------------
DROP TABLE IF EXISTS `google_map_log`;
CREATE TABLE `google_map_log`  (
  `gml_id` int(11) NOT NULL AUTO_INCREMENT,
  `gml_user_ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `gml_request_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `gml_request_payload` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `gml_google_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `gml_google_url` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `gml_google_request` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `gml_google_request_method` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `gml_google_response` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `gml_created_at` timestamp(0) NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP(0),
  `gml_updated_at` datetime(0) DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`gml_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
