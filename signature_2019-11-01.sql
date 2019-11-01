# ************************************************************
# Sequel Pro SQL dump
# Version 5438
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 8.0.15)
# Database: signature
# Generation Time: 2019-11-01 07:02:18 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table apple
# ------------------------------------------------------------

CREATE TABLE `apple` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '苹果开发者账号',
  `secret_key` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '开发者账号密码',
  `p12_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'p12地址',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table device
# ------------------------------------------------------------

CREATE TABLE `device` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int(11) DEFAULT '0' COMMENT '包ID',
  `apple_id` int(11) DEFAULT '0' COMMENT '苹果账号ID',
  `udid` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '设备udid',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `device` WRITE;
/*!40000 ALTER TABLE `device` DISABLE KEYS */;

INSERT INTO `device` (`id`, `package_id`, `apple_id`, `udid`, `created_at`, `updated_at`)
VALUES
	(3,0,0,'2323','2019-11-01 14:47:22','2019-11-01 14:47:22');

/*!40000 ALTER TABLE `device` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table package
# ------------------------------------------------------------

CREATE TABLE `package` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '包名',
  `icon` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '包ICON',
  `version` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '版本',
  `buddle_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '安装包ID',
  `ipa_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ipa地址',
  `certificate_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '证书地址',
  `certificate_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '证书ID',
  `introduction` text COLLATE utf8mb4_general_ci COMMENT '简介',
  `download_num` int(11) DEFAULT NULL COMMENT '下载量',
  `download_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '下载地址',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `package` WRITE;
/*!40000 ALTER TABLE `package` DISABLE KEYS */;

INSERT INTO `package` (`id`, `name`, `icon`, `version`, `buddle_id`, `ipa_url`, `certificate_url`, `certificate_id`, `introduction`, `download_num`, `download_url`, `created_at`, `updated_at`)
VALUES
	(2,NULL,NULL,NULL,NULL,'http://localhost:8000/storage/5dbbd6a5543c8.jpg',NULL,NULL,'23232323444',NULL,NULL,'2019-11-01 14:48:59','2019-11-01 14:54:40');

/*!40000 ALTER TABLE `package` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `productids` varchar(4068) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `origin_password` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '原密码',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `users_email_unique` (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `productids`, `origin_password`, `created_at`, `updated_at`)
VALUES
	(1,'admin','admin@admin.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','26i3VJaFLhVrlbSIT6iPxW7dU0ejEIIwfbWfUSq02LoUrsAfdUMUh4euuaCg','1,2,3,4,9,10,11,12,13,14,15,17,19,21,22,100,1000,1002,1004,1006,1008,1010,1012,1014,1100,1102,1104,1106,1108,1109,1111,1113,1115,1116,1118,1120,1122,1126,1128,1130,1132,1134,1136,1138,1141,1143,1145,1146,1148,1150,1152,1156,1158,1159,1161,1163,1164,1166,1167,1169,1170,1171,1173,1175,1177,1179,1180,1182,1183,1185','123456','2016-07-07 07:38:31','2019-03-18 11:24:58'),
	(2,'yh001','yh001@yh001.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','aBfhoyZtDN2xfD0dEmJGBSrmylDTBcIXmJxyTorG3JZuVZBOYYL2PtXOdVjp',NULL,'123456','2019-03-13 10:11:49','2019-04-02 11:33:47'),
	(3,'yh002','yh002@yh002.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','UmfQuXNyY9m1m69udOQculVYFD1EKZyueI9rCpb3exlTsdkWQ2OjkzDd1OB4','1,2,3,4,9,10,11,12,13,14,15,17,19,21,22,100,1000,1002,1004,1006,1008,1010,1012,1014,1100,1102,1104,1106,1108,1109,1111,1113,1115,1116,1118,1120,1122,1126,1128,1130,1132,1134,1136,1138,1141,1143,1145,1146,1148,1150,1152,1156,1158,1159,1161,1163,1164,1166,1167,1169,1170,1171,1173,1175,1177,1179,1180,1182,1183,1185','123456','2019-03-13 10:12:30','2019-04-02 17:33:36'),
	(4,'yh003','yh003@yh003.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','cl1KJGH4hbnYJaRMdnhfbulnXobGqXjncr0ndGn6Ma0mbSsBbvnP2r2tS4nz',NULL,'123456','2019-03-18 11:24:45','2019-04-02 13:31:47'),
	(5,'jim','Jim.Chuang@huntsman.com.tw','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','e6YZk7mAx02UF5alV6tRWgySTmkX4vFAMKA7NEvw6lF95MmXBEL3I0QpNMSi',NULL,'123456','2019-03-27 18:54:37','2019-04-01 14:12:41'),
	(6,'yh004','yh004@yh004.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','wUQoBM377E89H5KuQ13mI1uEjbPERwHALw7Q8nypG7YGTdKsI69CTisbypch',NULL,'123456','2019-03-28 09:25:15','2019-04-02 10:35:28'),
	(7,'admin001','admin001@admin001.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','y8AlPEjI5Ws884hZFXTbjh8uwHg3c2OkzOOHr9uac6DqeDA6DZtsG3SmdzY7',NULL,'123456','2019-03-28 10:31:32','2019-04-02 11:33:23'),
	(8,'gusest','gusest@com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','tvzWdCjajeDt8hC6y3lq1EthToH9LQT49E3BvpHMv7h3jl82lDRWa3hDYFSX',NULL,'123456','2019-03-28 11:06:12','2019-03-29 11:43:02'),
	(9,'guanliyuan001@guanliyuan001.com','guanliyuan001@guanliyuan001.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','umg3H5VaYN39vjKszTnl4Hx79h8J95h87BTveNqjJ5iuywkHCQsGJyMb57F4',NULL,'123456','2019-03-29 17:40:06','2019-04-04 21:08:45'),
	(10,'pedro','bard@bard.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','ujfJT2YlPihB1Imabqy9AQ0SLKVAKThgX6eAAQkCg68Dn7YSXMWPyFDq59Kw',NULL,'123456','2019-04-01 14:09:28','2019-04-02 10:41:12');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
