# ************************************************************
# Sequel Pro SQL dump
# Version 5438
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 129.226.163.223 (MySQL 5.7.26-log)
# Database: signature
# Generation Time: 2019-11-21 01:15:15 +0000
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
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  `account` varchar(255) DEFAULT NULL COMMENT '苹果开发者账号',
  `udid_num` int(11) DEFAULT '0' COMMENT '用户设备数量',
  `secret_key` varchar(255) DEFAULT NULL COMMENT '开发者账号密码',
  `p12_url` varchar(255) DEFAULT NULL COMMENT 'p12地址',
  `certificate_id` varchar(255) DEFAULT NULL COMMENT '证书ID',
  `buddle_id` varchar(255) DEFAULT NULL COMMENT 'buddleID',
  `status` tinyint(1) DEFAULT '2' COMMENT '1:有效；2:需要验证',
  `is_push` tinyint(1) DEFAULT '0' COMMENT '是否是推送证书0:否；1:是',
  `created_at` timestamp NOT NULL DEFAULT '1999-01-01 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `apple` WRITE;
/*!40000 ALTER TABLE `apple` DISABLE KEYS */;

INSERT INTO `apple` (`id`, `user_id`, `account`, `udid_num`, `secret_key`, `p12_url`, `certificate_id`, `buddle_id`, `status`, `is_push`, `created_at`, `updated_at`)
VALUES
	(3,10,'rpaz23@163.com',11,'Developer123456','http://www.677677.club/storage/5dcb9add0ffe5.p12','CYL58XBX6H','com.siyan.secretalbum',2,0,'2019-11-04 13:27:29','2019-11-20 14:18:41'),
	(4,10,'beng57539113@163.com',31,'Gary@1728','http://www.677677.club/storage/5dd3531d1a69e.p12','TD236HZAM2',NULL,2,1,'2019-11-05 03:15:17','2019-11-20 14:18:42'),
	(5,10,'ydoknm@163.com',26,'Zxc112211','http://www.677677.club/storage/5dd37b0e2fbbb.p12','WJ34XKGF7N',NULL,2,1,'2019-11-19 13:18:06','2019-11-20 14:23:24');

/*!40000 ALTER TABLE `apple` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table device
# ------------------------------------------------------------

CREATE TABLE `device` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  `package_id` int(11) DEFAULT '0' COMMENT '包ID',
  `apple_id` int(11) DEFAULT '0' COMMENT '苹果账号ID',
  `udid` varchar(255) DEFAULT NULL COMMENT '设备udid',
  `ipa_url` varchar(255) DEFAULT NULL COMMENT 'ipa包下载地址',
  `plist_url` varchar(255) DEFAULT NULL COMMENT 'plist地址',
  `buddle_id` varchar(255) DEFAULT NULL COMMENT 'buddleID',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `device` WRITE;
/*!40000 ALTER TABLE `device` DISABLE KEYS */;

INSERT INTO `device` (`id`, `user_id`, `package_id`, `apple_id`, `udid`, `ipa_url`, `plist_url`, `buddle_id`, `created_at`, `updated_at`)
VALUES
	(3,10,2,3,'8d17e5b18e24fab9a7279a0a6ea3f6167814638e','https://www.677677.club/applesign/rpaz23@163.com/CYL58XBX6H/0/8d17e5b18e24fab9a7279a0a6ea3f6167814638e_20191120162125.ipa','https://www.677677.club//applesign/rpaz23@163.com/CYL58XBX6H/0/8d17e5b18e24fab9a7279a0a6ea3f6167814638e_20191120162125.plist','com.siyan.secretalbum.dfc_wx_3','2019-11-20 16:21:25','2019-11-20 16:21:25'),
	(4,10,2,3,'00008030-000661992140802E','https://www.677677.club/applesign/rpaz23@163.com/CYL58XBX6H/0/00008030-000661992140802E_20191120163436.ipa','https://www.677677.club//applesign/rpaz23@163.com/CYL58XBX6H/0/00008030-000661992140802E_20191120163436.plist','com.siyan.secretalbum.dfc_wx_3','2019-11-20 16:34:40','2019-11-20 16:34:40'),
	(5,10,3,3,'c235829e243dd4f1b100b505de21d302a18aff2f','https://www.677677.club/applesign/rpaz23@163.com/CYL58XBX6H/0/c235829e243dd4f1b100b505de21d302a18aff2f_20191120164022.ipa','https://www.677677.club//applesign/rpaz23@163.com/CYL58XBX6H/0/c235829e243dd4f1b100b505de21d302a18aff2f_20191120164022.plist','com.weliao1.weliao1.dfc_wx_3','2019-11-20 16:40:38','2019-11-20 16:40:38'),
	(8,10,3,4,'9efa99314d8da5632a37dfa2abad6ac5cedb715e','https://www.677677.club/applesign/beng57539113@163.com/TD236HZAM2/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191120164130.ipa','https://www.677677.club//applesign/beng57539113@163.com/TD236HZAM2/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191120164130.plist','com.weliao1.weliao1.dfc_wx_4','2019-11-20 16:41:39','2019-11-20 16:41:39'),
	(10,10,2,3,'9efa99314d8da5632a37dfa2abad6ac5cedb715e','https://www.677677.club/applesign/rpaz23@163.com/CYL58XBX6H/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191120170719.ipa','https://www.677677.club//applesign/rpaz23@163.com/CYL58XBX6H/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191120170719.plist','com.siyan.secretalbum.dfc_wx_3','2019-11-20 17:07:20','2019-11-20 17:07:20'),
	(11,10,2,3,'c235829e243dd4f1b100b505de21d302a18aff2f','https://www.677677.club/applesign/rpaz23@163.com/CYL58XBX6H/0/c235829e243dd4f1b100b505de21d302a18aff2f_20191120162233.ipa','https://www.677677.club//applesign/rpaz23@163.com/CYL58XBX6H/0/c235829e243dd4f1b100b505de21d302a18aff2f_20191120162233.plist','com.siyan.secretalbum.dfc_wx_3','2019-11-20 16:24:15','2019-11-20 16:24:15'),
	(12,10,3,4,'00008020-001245D90CD8002E','https://www.677677.club/applesign/rpaz23@163.com/CYL58XBX6H/0/c235829e243dd4f1b100b505de21d302a18aff2f_20191120162233.ipa','https://www.677677.club//applesign/rpaz23@163.com/CYL58XBX6H/0/c235829e243dd4f1b100b505de21d302a18aff2f_20191120162233.plist','com.weliao1.weliao1.dfc_wx_4','2019-11-20 16:24:57','2019-11-20 16:24:57'),
	(14,10,8,5,'9efa99314d8da5632a37dfa2abad6ac5cedb715e','https://www.677677.club/applesign/ydoknm@163.com/WJ34XKGF7N/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191120170639.ipa','https://www.677677.club//applesign/ydoknm@163.com/WJ34XKGF7N/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191120170639.plist','com.zhb4.zhb4','2019-11-20 17:06:45','2019-11-20 17:06:45'),
	(15,10,8,5,'00008020-001245D90CD8002E','https://www.677677.club/applesign/rpaz23@163.com/CYL58XBX6H/0/c235829e243dd4f1b100b505de21d302a18aff2f_20191120162233.ipa','https://www.677677.club//applesign/rpaz23@163.com/CYL58XBX6H/0/c235829e243dd4f1b100b505de21d302a18aff2f_20191120162233.plist','com.zhb4.zhb4','2019-11-20 16:26:52','2019-11-20 16:26:52'),
	(17,10,6,3,'9efa99314d8da5632a37dfa2abad6ac5cedb715e','https://www.677677.club/applesign/rpaz23@163.com/CYL58XBX6H/0/c235829e243dd4f1b100b505de21d302a18aff2f_20191120162233.ipa','https://www.677677.club//applesign/rpaz23@163.com/CYL58XBX6H/0/c235829e243dd4f1b100b505de21d302a18aff2f_20191120162233.plist','com.hudapang.DaBaoCeShi.dfc_wx_3','2019-11-20 16:27:21','2019-11-20 16:27:21'),
	(18,10,2,3,'sadasdad','https://www.677677.club/applesign/rpaz23@163.com/CYL58XBX6H/0/c235829e243dd4f1b100b505de21d302a18aff2f_20191120162233.ipa','https://www.677677.club//applesign/rpaz23@163.com/CYL58XBX6H/0/c235829e243dd4f1b100b505de21d302a18aff2f_20191120162233.plist','com.siyan.secretalbum.dfc_wx_3','2019-11-20 16:27:52','2019-11-20 16:27:52'),
	(19,10,2,3,'390ed8448e1142384e1ac052a103e5cfc7b11dc0','https://www.677677.club/applesign/rpaz23@163.com/CYL58XBX6H/0/390ed8448e1142384e1ac052a103e5cfc7b11dc0_20191120163925.ipa','https://www.677677.club//applesign/rpaz23@163.com/CYL58XBX6H/0/390ed8448e1142384e1ac052a103e5cfc7b11dc0_20191120163925.plist','com.siyan.secretalbum.dfc_wx_3','2019-11-20 16:39:25','2019-11-20 16:39:25'),
	(20,10,8,5,'0cefc7ac382916b1847e39d96d2b8fa0717c68b9','https://www.677677.club/applesign/ydoknm@163.com/WJ34XKGF7N/0/0cefc7ac382916b1847e39d96d2b8fa0717c68b9_20191120163818.ipa','https://www.677677.club//applesign/ydoknm@163.com/WJ34XKGF7N/0/0cefc7ac382916b1847e39d96d2b8fa0717c68b9_20191120163818.plist','com.zhb4.zhb4','2019-11-20 16:38:30','2019-11-20 16:38:30'),
	(21,10,8,5,'390ed8448e1142384e1ac052a103e5cfc7b11dc0','https://www.677677.club/applesign/ydoknm@163.com/WJ34XKGF7N/0/390ed8448e1142384e1ac052a103e5cfc7b11dc0_20191120163850.ipa','https://www.677677.club//applesign/ydoknm@163.com/WJ34XKGF7N/0/390ed8448e1142384e1ac052a103e5cfc7b11dc0_20191120163850.plist','com.zhb4.zhb4','2019-11-20 16:39:04','2019-11-20 16:39:04'),
	(22,10,8,5,'fca6d7087e100fa6087cd8c5dab72620559f91fe','https://www.677677.club/applesign/ydoknm@163.com/WJ34XKGF7N/0/fca6d7087e100fa6087cd8c5dab72620559f91fe_20191120163729.ipa','https://www.677677.club//applesign/ydoknm@163.com/WJ34XKGF7N/0/fca6d7087e100fa6087cd8c5dab72620559f91fe_20191120163729.plist','com.zhb4.zhb4','2019-11-20 16:37:35','2019-11-20 16:37:35');

/*!40000 ALTER TABLE `device` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table package
# ------------------------------------------------------------

CREATE TABLE `package` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  `name` varchar(255) DEFAULT NULL COMMENT '包名',
  `icon` varchar(255) DEFAULT NULL COMMENT '包ICON',
  `version` varchar(255) DEFAULT NULL COMMENT '版本',
  `buddle_id` varchar(255) DEFAULT NULL COMMENT '安装包ID',
  `apple_id` int(11) DEFAULT NULL COMMENT '苹果账号ID',
  `ipa_url` varchar(255) DEFAULT NULL COMMENT 'ipa地址',
  `certificate_url` varchar(255) DEFAULT NULL COMMENT '证书地址',
  `certificate_id` varchar(255) DEFAULT NULL COMMENT '证书ID',
  `introduction` text COMMENT '简介',
  `download_num` int(11) DEFAULT NULL COMMENT '下载量',
  `download_url` varchar(255) DEFAULT NULL COMMENT '下载地址',
  `is_push` tinyint(1) DEFAULT '0' COMMENT '0:不需要；1:需要推送',
  `size` varchar(255) DEFAULT NULL COMMENT '包大小',
  `created_at` timestamp NULL DEFAULT '1990-01-01 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `package` WRITE;
/*!40000 ALTER TABLE `package` DISABLE KEYS */;

INSERT INTO `package` (`id`, `user_id`, `name`, `icon`, `version`, `buddle_id`, `apple_id`, `ipa_url`, `certificate_url`, `certificate_id`, `introduction`, `download_num`, `download_url`, `is_push`, `size`, `created_at`, `updated_at`)
VALUES
	(2,10,'secretalbum','http://www.677677.club/storage/5dd355988a82f.jpg','v1.1','com.siyan.secretalbum',NULL,'http://www.677677.club/storage/5dccb60f7e6da.ipa',NULL,NULL,'23232323444',NULL,NULL,0,'2.3M','2019-11-19 02:38:16','2019-11-20 14:18:49'),
	(3,10,'rings','http://www.677677.club/storage/5dcb9bab42546.jpg','v1.1','com.weliao1.weliao1',NULL,'http://www.677677.club/storage/5dd1ee6bed1bb.ipa',NULL,NULL,'Rings',NULL,NULL,1,'34M','2019-11-19 01:36:52','2019-11-20 14:18:50'),
	(4,10,'fazhi',NULL,'v1.1','com.siyan.secretalbum',NULL,'http://49.235.90.84:8893/storage/5dc25fe331903.ipa',NULL,NULL,'222',NULL,NULL,0,NULL,'2019-11-06 14:02:06','2019-11-20 14:18:51'),
	(5,10,'ccc',NULL,'v1.1','com.weliao1.weliao1',NULL,'http://49.235.90.84:8893/storage/5dc27a405325a.ipa',NULL,NULL,'ddd',NULL,NULL,0,NULL,'2019-11-06 15:46:08','2019-11-20 14:18:53'),
	(6,10,'DaBaoCeShi',NULL,'1.0.0','com.hudapang.DaBaoCeShi',NULL,'http://49.235.90.84:8893/storage/5dc27f435b249.ipa',NULL,NULL,'空包测试',NULL,NULL,0,NULL,'2019-11-06 16:07:31','2019-11-20 14:18:55'),
	(7,10,'DaBaoCeShi2',NULL,'1.0.0','com.hudapang.DaBaoCeShi',NULL,'http://49.235.90.84:8893/storage/5dc28a5512372.ipa',NULL,NULL,'空包测试添加icon和启动图',NULL,NULL,0,NULL,'2019-11-06 08:55:48','2019-11-20 14:18:57'),
	(8,10,'Rings','http://677677.club/storage/5dd3e984e9f2d.gif','1.0.4','com.zhb4.zhb4',5,'http://www.677677.club/storage/5dd36124f279f.ipa',NULL,NULL,'ssss2',NULL,NULL,1,NULL,'2019-11-19 21:09:24','2019-11-20 14:18:58');

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
  `role` tinyint(1) DEFAULT '1' COMMENT '1:普通用户；-9:超级管理员',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `users_email_unique` (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `productids`, `origin_password`, `role`, `created_at`, `updated_at`)
VALUES
	(1,'admin','admin@admin.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','26i3VJaFLhVrlbSIT6iPxW7dU0ejEIIwfbWfUSq02LoUrsAfdUMUh4euuaCg','1,2,3,4,9,10,11,12,13,14,15,17,19,21,22,100,1000,1002,1004,1006,1008,1010,1012,1014,1100,1102,1104,1106,1108,1109,1111,1113,1115,1116,1118,1120,1122,1126,1128,1130,1132,1134,1136,1138,1141,1143,1145,1146,1148,1150,1152,1156,1158,1159,1161,1163,1164,1166,1167,1169,1170,1171,1173,1175,1177,1179,1180,1182,1183,1185','123456',-9,'2016-07-07 07:38:31','2019-03-18 11:24:58'),
	(2,'yh001','yh001@yh001.com','$2y$10$HbFv7qKtZTo2Qk2/VYy0dOi7RHxTMp..FE8L9qgQSQchYu2u10AWa','aBfhoyZtDN2xfD0dEmJGBSrmylDTBcIXmJxyTorG3JZuVZBOYYL2PtXOdVjp',NULL,'1234567',1,'2019-11-20 06:35:53','2019-11-20 06:35:53'),
	(3,'yh002','yh002@yh002.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','UmfQuXNyY9m1m69udOQculVYFD1EKZyueI9rCpb3exlTsdkWQ2OjkzDd1OB4','1,2,3,4,9,10,11,12,13,14,15,17,19,21,22,100,1000,1002,1004,1006,1008,1010,1012,1014,1100,1102,1104,1106,1108,1109,1111,1113,1115,1116,1118,1120,1122,1126,1128,1130,1132,1134,1136,1138,1141,1143,1145,1146,1148,1150,1152,1156,1158,1159,1161,1163,1164,1166,1167,1169,1170,1171,1173,1175,1177,1179,1180,1182,1183,1185','123456',1,'2019-03-13 10:12:30','2019-04-02 17:33:36'),
	(4,'yh003','yh003@yh003.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','cl1KJGH4hbnYJaRMdnhfbulnXobGqXjncr0ndGn6Ma0mbSsBbvnP2r2tS4nz',NULL,'123456',1,'2019-03-18 11:24:45','2019-04-02 13:31:47'),
	(5,'jim','Jim.Chuang@huntsman.com.tw','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','e6YZk7mAx02UF5alV6tRWgySTmkX4vFAMKA7NEvw6lF95MmXBEL3I0QpNMSi',NULL,'123456',1,'2019-03-27 18:54:37','2019-04-01 14:12:41'),
	(6,'yh004','yh004@yh004.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','wUQoBM377E89H5KuQ13mI1uEjbPERwHALw7Q8nypG7YGTdKsI69CTisbypch',NULL,'123456',1,'2019-03-28 09:25:15','2019-04-02 10:35:28'),
	(7,'admin001','admin001@admin001.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','y8AlPEjI5Ws884hZFXTbjh8uwHg3c2OkzOOHr9uac6DqeDA6DZtsG3SmdzY7',NULL,'123456',1,'2019-03-28 10:31:32','2019-04-02 11:33:23'),
	(8,'gusest','gusest@com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','tvzWdCjajeDt8hC6y3lq1EthToH9LQT49E3BvpHMv7h3jl82lDRWa3hDYFSX',NULL,'123456',1,'2019-03-28 11:06:12','2019-03-29 11:43:02'),
	(9,'guanliyuan001@guanliyuan001.com','guanliyuan001@guanliyuan001.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','umg3H5VaYN39vjKszTnl4Hx79h8J95h87BTveNqjJ5iuywkHCQsGJyMb57F4',NULL,'123456',1,'2019-03-29 17:40:06','2019-04-04 21:08:45'),
	(10,'pedro','bard@bard.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','iSJv2mqgkXQDTTOBGwZhRMTATGsV0O7wvuoICKZzntk3WXfGgola8LwmuqY9',NULL,'123456',1,'2019-04-01 14:09:28','2019-04-02 10:41:12');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
