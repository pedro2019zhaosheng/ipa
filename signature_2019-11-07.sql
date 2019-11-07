# ************************************************************
# Sequel Pro SQL dump
# Version 5438
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 49.235.90.84 (MySQL 5.5.62-log)
# Database: signature
# Generation Time: 2019-11-07 07:25:37 +0000
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
  `account` varchar(255) DEFAULT NULL COMMENT '苹果开发者账号',
  `udid_num` int(11) DEFAULT '0' COMMENT '用户设备数量',
  `secret_key` varchar(255) DEFAULT NULL COMMENT '开发者账号密码',
  `p12_url` varchar(255) DEFAULT NULL COMMENT 'p12地址',
  `certificate_id` varchar(255) DEFAULT NULL COMMENT '证书ID',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `apple` WRITE;
/*!40000 ALTER TABLE `apple` DISABLE KEYS */;

INSERT INTO `apple` (`id`, `account`, `udid_num`, `secret_key`, `p12_url`, `certificate_id`, `created_at`, `updated_at`)
VALUES
	(3,'rpaz23@163.com',6,'Developer123456','http://49.235.90.84:8893/storage/5dc2180530c01.p12','CYL58XBX6H','2019-11-04 13:27:29','2019-11-07 10:48:48'),
	(4,'beng57539113@163.com',29,'Gary@1728','http://49.235.90.84:8893/storage/5dc292a374d6f.p12','TD236HZAM2','2019-11-05 03:15:17','2019-11-07 10:46:11');

/*!40000 ALTER TABLE `apple` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table apple_developer
# ------------------------------------------------------------

CREATE TABLE `apple_developer` (
  `apuid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '开发者id',
  `user` varchar(255) DEFAULT '' COMMENT '开发者帐号',
  `password` varchar(255) DEFAULT '' COMMENT '密码',
  `phone` varchar(255) DEFAULT '' COMMENT '手机号',
  `uuid_num` int(11) DEFAULT '1' COMMENT '用户设备数量',
  `status` int(11) DEFAULT '1' COMMENT '1.正常, 2.禁用',
  `checked` int(11) DEFAULT '2' COMMENT '1.已信任服务器, 2.未信任设备',
  `admin_id` int(11) DEFAULT '2' COMMENT '管理员id',
  `c_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`apuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='开发者账号';



# Dump of table apple_developer_cer
# ------------------------------------------------------------

CREATE TABLE `apple_developer_cer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `apuid` int(11) DEFAULT '0' COMMENT '开发者id',
  `certificate_id` varchar(255) DEFAULT '' COMMENT '证书id',
  `certificate_pem` varchar(255) DEFAULT '' COMMENT '证书签名',
  `key_pem` varchar(255) DEFAULT '' COMMENT '签名私钥',
  `c_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `e_time` datetime DEFAULT NULL COMMENT '结束时间',
  `status` int(11) DEFAULT '1' COMMENT '1.正常, 2.禁用',
  `admin_id` int(11) DEFAULT '1' COMMENT '管理员id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='证书表';



# Dump of table apple_developer_ipa_log
# ------------------------------------------------------------

CREATE TABLE `apple_developer_ipa_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` int(11) DEFAULT '0' COMMENT '平台用户uid',
  `gid` int(11) DEFAULT '0' COMMENT '平台gid',
  `apuid` int(11) DEFAULT '0' COMMENT '开发者id',
  `uuid` varchar(255) DEFAULT '' COMMENT '开发者帐号',
  `certificate_id` varchar(255) DEFAULT '' COMMENT '苹果证书id',
  `certificate_pem` varchar(255) DEFAULT '' COMMENT '苹果客户端证书路径',
  `key_pem` varchar(255) DEFAULT '' COMMENT '苹果私钥路径',
  `mobileprovision` varchar(255) DEFAULT '' COMMENT '授权证书路径',
  `source_ipa` varchar(255) DEFAULT '' COMMENT '原包',
  `to_ipa` varchar(255) DEFAULT '' COMMENT '打包后新包',
  `plist` varchar(255) DEFAULT NULL COMMENT 'plist',
  `build_id` varchar(255) DEFAULT NULL COMMENT 'build_id',
  `status` int(11) DEFAULT '1' COMMENT '1.正常, 2.禁用',
  `c_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `e_time` datetime DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='打包日志表';



# Dump of table apple_developer_mobileprovision
# ------------------------------------------------------------

CREATE TABLE `apple_developer_mobileprovision` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `apuid` int(11) DEFAULT '0' COMMENT '开发者id',
  `certificate_id` varchar(255) DEFAULT '' COMMENT 'certificate证书 Id',
  `build_id` varchar(255) DEFAULT '' COMMENT 'build_id',
  `mobileprovision` varchar(255) DEFAULT '' COMMENT 'mobileprovision',
  `status` int(11) DEFAULT '1' COMMENT '1.正常, 2.禁用',
  `c_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table apple_developer_uuid
# ------------------------------------------------------------

CREATE TABLE `apple_developer_uuid` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `apuid` int(11) DEFAULT '0' COMMENT '开发者id',
  `uuid` varchar(255) DEFAULT '' COMMENT '用户设备码',
  `status` int(11) DEFAULT '1' COMMENT '1.正常, 2.禁用',
  `c_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table device
# ------------------------------------------------------------

CREATE TABLE `device` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int(11) DEFAULT '0' COMMENT '包ID',
  `apple_id` int(11) DEFAULT '0' COMMENT '苹果账号ID',
  `udid` varchar(255) DEFAULT NULL COMMENT '设备udid',
  `ipa_url` varchar(255) DEFAULT NULL COMMENT 'ipa包下载地址',
  `plist_url` varchar(255) DEFAULT NULL COMMENT 'plist地址',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `device` WRITE;
/*!40000 ALTER TABLE `device` DISABLE KEYS */;

INSERT INTO `device` (`id`, `package_id`, `apple_id`, `udid`, `ipa_url`, `plist_url`, `created_at`, `updated_at`)
VALUES
	(1,2,3,'9efa99314d8da5632a37dfa2abad6ac5cedb715e','https://test.daoyuancloud.com/applesign/rpaz23@163.com/CYL58XBX6H/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191107104858.ipa','https://test.daoyuancloud.com//applesign/rpaz23@163.com/CYL58XBX6H/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191107104858.plist','2019-11-07 02:48:59','2019-11-07 10:48:59'),
	(2,2,3,'33333','https://test.daoyuancloud.com/applesign/rpaz23@163.com/CYL58XBX6H/0/33333_20191107144608.ipa','https://test.daoyuancloud.com//applesign/rpaz23@163.com/CYL58XBX6H/0/33333_20191107144608.plist','2019-11-07 06:46:10','2019-11-07 14:46:10'),
	(3,2,3,'33333','https://test.daoyuancloud.com/applesign/rpaz23@163.com/CYL58XBX6H/0/33333_20191107144617.ipa','https://test.daoyuancloud.com//applesign/rpaz23@163.com/CYL58XBX6H/0/33333_20191107144617.plist','2019-11-07 06:46:18','2019-11-07 14:46:18');

/*!40000 ALTER TABLE `device` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table package
# ------------------------------------------------------------

CREATE TABLE `package` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '包名',
  `icon` varchar(255) DEFAULT NULL COMMENT '包ICON',
  `version` varchar(255) DEFAULT NULL COMMENT '版本',
  `buddle_id` varchar(255) DEFAULT NULL COMMENT '安装包ID',
  `ipa_url` varchar(255) DEFAULT NULL COMMENT 'ipa地址',
  `certificate_url` varchar(255) DEFAULT NULL COMMENT '证书地址',
  `certificate_id` varchar(255) DEFAULT NULL COMMENT '证书ID',
  `introduction` text COMMENT '简介',
  `download_num` int(11) DEFAULT NULL COMMENT '下载量',
  `download_url` varchar(255) DEFAULT NULL COMMENT '下载地址',
  `created_at` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `package` WRITE;
/*!40000 ALTER TABLE `package` DISABLE KEYS */;

INSERT INTO `package` (`id`, `name`, `icon`, `version`, `buddle_id`, `ipa_url`, `certificate_url`, `certificate_id`, `introduction`, `download_num`, `download_url`, `created_at`, `updated_at`)
VALUES
	(2,NULL,NULL,NULL,'com.siyan.secretalbum','http://49.235.90.84:8893/storage/5dc218145a028.ipa',NULL,NULL,'23232323444',NULL,NULL,'2019-11-01 14:48:59','2019-11-06 10:27:01'),
	(3,'rings','http://49.235.90.84:8893/storage/5dc22f45e7106.jpg','v1.1','com.weliao1.weliao1','http://49.235.90.84:8893/storage/5dc2951e8b669.ipa',NULL,NULL,'Rings',NULL,NULL,'2019-11-06 09:40:46','2019-11-06 17:40:46'),
	(4,'fazhi',NULL,'v1.1','com.siyan.secretalbum','http://49.235.90.84:8893/storage/5dc25fe331903.ipa',NULL,NULL,'222',NULL,NULL,'2019-11-06 14:02:06','2019-11-06 16:20:41'),
	(5,'ccc',NULL,'v1.1','com.weliao1.weliao1','http://49.235.90.84:8893/storage/5dc27a405325a.ipa',NULL,NULL,'ddd',NULL,NULL,'2019-11-06 15:46:08','2019-11-06 16:20:38'),
	(6,'DaBaoCeShi',NULL,'1.0.0','com.hudapang.DaBaoCeShi','http://49.235.90.84:8893/storage/5dc27f435b249.ipa',NULL,NULL,'空包测试',NULL,NULL,'2019-11-06 16:07:31','2019-11-06 16:20:35'),
	(7,'DaBaoCeShi2',NULL,'1.0.0','com.hudapang.DaBaoCeShi','http://49.235.90.84:8893/storage/5dc28a5512372.ipa',NULL,NULL,'空包测试添加icon和启动图',NULL,NULL,'2019-11-06 08:55:48','2019-11-06 16:55:48');

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
