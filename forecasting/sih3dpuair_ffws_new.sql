/*
 Navicat Premium Dump SQL

 Source Server         : SIH3 - Server baru
 Source Server Type    : MySQL
 Source Server Version : 80043 (8.0.43-0ubuntu0.24.04.1)
 Source Host           : 10.242.100.200:3306
 Source Schema         : sih3dpuair_ffws_new

 Target Server Type    : MySQL
 Target Server Version : 80043 (8.0.43-0ubuntu0.24.04.1)
 File Encoding         : 65001

 Date: 01/09/2025 10:25:04
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for data_actual
-- ----------------------------
DROP TABLE IF EXISTS `data_actual`;
CREATE TABLE `data_actual`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `mas_sensor_id` bigint NOT NULL,
  `mas_sensor_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `value` double NOT NULL,
  `received_at` datetime NOT NULL,
  `threshold_status` enum('safe','warning','danger') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `mas_sensor_id`(`mas_sensor_id` ASC) USING BTREE,
  INDEX `received_at`(`received_at` ASC) USING BTREE,
  CONSTRAINT `data_actual_ibfk_1` FOREIGN KEY (`mas_sensor_id`) REFERENCES `mas_sensors` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for data_prediction
-- ----------------------------
DROP TABLE IF EXISTS `data_prediction`;
CREATE TABLE `data_prediction`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `mas_sensor_id` bigint NOT NULL,
  `mas_sensor_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `mas_model_id` bigint NOT NULL,
  `prediction_run_at` datetime NOT NULL,
  `prediction_for_ts` datetime NOT NULL,
  `predicted_value` double NOT NULL,
  `confidence_score` double NULL DEFAULT NULL,
  `threshold_prediction_status` enum('safe','warning','danger') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `mas_model_id`(`mas_model_id` ASC) USING BTREE,
  INDEX `mas_sensor_id`(`mas_sensor_id` ASC) USING BTREE,
  INDEX `prediction_run_at`(`prediction_run_at` ASC) USING BTREE,
  CONSTRAINT `data_prediction_ibfk_1` FOREIGN KEY (`mas_sensor_id`) REFERENCES `mas_sensors` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `data_prediction_ibfk_2` FOREIGN KEY (`mas_model_id`) REFERENCES `mas_models` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for mas_devices
-- ----------------------------
DROP TABLE IF EXISTS `mas_devices`;
CREATE TABLE `mas_devices`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `mas_river_basin_id` bigint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `elevation_m` double NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `mas_river_basin_id`(`mas_river_basin_id` ASC) USING BTREE,
  CONSTRAINT `mas_devices_ibfk_1` FOREIGN KEY (`mas_river_basin_id`) REFERENCES `mas_river_basins` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for mas_models
-- ----------------------------
DROP TABLE IF EXISTS `mas_models`;
CREATE TABLE `mas_models`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `n_steps_in` tinyint NULL DEFAULT NULL,
  `n_steps_out` tinyint NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for mas_river_basins
-- ----------------------------
DROP TABLE IF EXISTS `mas_river_basins`;
CREATE TABLE `mas_river_basins`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for mas_scalers
-- ----------------------------
DROP TABLE IF EXISTS `mas_scalers`;
CREATE TABLE `mas_scalers`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `mas_model_id` bigint NOT NULL,
  `mas_sensor_id` bigint NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `io_axis` enum('x','y') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `technique` enum('standard','minmax','robust','custom') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'custom',
  `version` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `file_path` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `file_hash_sha256` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_model_sensor_axis_active`(`mas_model_id` ASC, `mas_sensor_id` ASC, `io_axis` ASC, `is_active` ASC) USING BTREE,
  INDEX `mas_model_id`(`mas_model_id` ASC) USING BTREE,
  INDEX `mas_sensor_id`(`mas_sensor_id` ASC) USING BTREE,
  INDEX `io_axis`(`io_axis` ASC) USING BTREE,
  CONSTRAINT `fk_scaler_model` FOREIGN KEY (`mas_model_id`) REFERENCES `mas_models` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_scaler_sensor` FOREIGN KEY (`mas_sensor_id`) REFERENCES `mas_sensors` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for mas_sensors
-- ----------------------------
DROP TABLE IF EXISTS `mas_sensors`;
CREATE TABLE `mas_sensors`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `device_id` bigint NOT NULL,
  `sensor_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `parameter` enum('water_level','rainfall') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `mas_model_id` bigint NULL DEFAULT NULL,
  `threshold_safe` double NULL DEFAULT NULL,
  `threshold_warning` double NULL DEFAULT NULL,
  `threshold_danger` double NULL DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `last_seen` datetime NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `device_id`(`device_id` ASC) USING BTREE,
  INDEX `mas_model_id`(`mas_model_id` ASC) USING BTREE,
  CONSTRAINT `mas_sensors_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `mas_devices` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `mas_sensors_ibfk_2` FOREIGN KEY (`mas_model_id`) REFERENCES `mas_models` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `role` enum('admin','operator','viewer') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `email`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for wa_blast
-- ----------------------------
DROP TABLE IF EXISTS `wa_blast`;
CREATE TABLE `wa_blast`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
