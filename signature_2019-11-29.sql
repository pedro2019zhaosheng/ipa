# ************************************************************
# Sequel Pro SQL dump
# Version 5438
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 129.226.163.223 (MySQL 5.7.26-log)
# Database: signature
# Generation Time: 2019-11-29 01:13:49 +0000
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
	(3,10,'rpaz23@163.com',11,'Developer123456','http://www.677677.club/storage/5dcb9add0ffe5.p12','CYL58XBX6H','com.siyan.secretalbum',1,1,'2019-11-04 13:27:29','2019-11-26 15:16:05'),
	(4,10,'beng57539113@163.com',31,'Gary@1728','http://www.677677.club/storage/5dd3531d1a69e.p12','TD236HZAM2',NULL,1,1,'2019-11-05 03:15:17','2019-11-26 15:15:59'),
	(5,10,'ydoknm@163.com',27,'Zxc112211','http://www.677677.club/storage/5dd37b0e2fbbb.p12','WJ34XKGF7N',NULL,1,1,'2019-11-19 13:18:06','2019-11-26 15:15:59'),
	(7,11,'shitqrjpe527203@163.com',4,'AUli22336','http://www.677677.club/storage/5ddb74048c939.p12','4JC7XAV83U',NULL,1,1,'2019-11-25 14:26:12','2019-11-26 15:16:00'),
	(8,11,'zhenxinyingu@163.com',0,'Guwu135791',NULL,NULL,NULL,1,1,'2019-11-26 14:20:14','2019-11-26 15:16:01');

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
	(1,11,9,7,'9efa99314d8da5632a37dfa2abad6ac5cedb715e','https://www.677677.club/applesign/shitqrjpe527203@163.com/4JC7XAV83U/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191127092721.ipa','https://www.677677.club//applesign/shitqrjpe527203@163.com/4JC7XAV83U/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191127092721.plist','com.dfc.xiaoshuo3333','2019-11-27 09:27:24','2019-11-27 09:27:24'),
	(2,11,15,7,'9efa99314d8da5632a37dfa2abad6ac5cedb715e','https://www.677677.club/applesign/shitqrjpe527203@163.com/4JC7XAV83U/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191127092748.ipa','https://www.677677.club//applesign/shitqrjpe527203@163.com/4JC7XAV83U/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191127092748.plist','com.siyan.secretalbum.dfc_wx_7','2019-11-27 09:27:49','2019-11-27 09:27:49'),
	(3,11,17,7,'9efa99314d8da5632a37dfa2abad6ac5cedb715e','https://www.677677.club/applesign/shitqrjpe527203@163.com/4JC7XAV83U/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191127092807.ipa','https://www.677677.club//applesign/shitqrjpe527203@163.com/4JC7XAV83U/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191127092807.plist','AA.pangpang.zhushou01','2019-11-27 09:28:08','2019-11-27 09:28:08'),
	(4,11,9,7,'8d17e5b18e24fab9a7279a0a6ea3f6167814638e','https://www.677677.club/applesign/shitqrjpe527203@163.com/4JC7XAV83U/0/8d17e5b18e24fab9a7279a0a6ea3f6167814638e_20191127094736.ipa','https://www.677677.club//applesign/shitqrjpe527203@163.com/4JC7XAV83U/0/8d17e5b18e24fab9a7279a0a6ea3f6167814638e_20191127094736.plist','com.dfc.xiaoshuo3333','2019-11-27 09:47:39','2019-11-27 09:47:39'),
	(5,11,15,7,'8d17e5b18e24fab9a7279a0a6ea3f6167814638e','https://www.677677.club/applesign/shitqrjpe527203@163.com/4JC7XAV83U/0/8d17e5b18e24fab9a7279a0a6ea3f6167814638e_20191127094756.ipa','https://www.677677.club//applesign/shitqrjpe527203@163.com/4JC7XAV83U/0/8d17e5b18e24fab9a7279a0a6ea3f6167814638e_20191127094756.plist','com.siyan.secretalbum.dfc_wx_7','2019-11-27 09:47:57','2019-11-27 09:47:57'),
	(6,11,17,7,'8d17e5b18e24fab9a7279a0a6ea3f6167814638e','https://www.677677.club/applesign/shitqrjpe527203@163.com/4JC7XAV83U/0/8d17e5b18e24fab9a7279a0a6ea3f6167814638e_20191127094819.ipa','https://www.677677.club//applesign/shitqrjpe527203@163.com/4JC7XAV83U/0/8d17e5b18e24fab9a7279a0a6ea3f6167814638e_20191127094819.plist','AA.pangpang.zhushou01','2019-11-27 09:48:20','2019-11-27 09:48:20');

/*!40000 ALTER TABLE `device` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table package
# ------------------------------------------------------------

CREATE TABLE `package` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT '0' COMMENT '父包id',
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
  `download_num` int(11) DEFAULT '0' COMMENT '下载量',
  `download_url` varchar(255) DEFAULT NULL COMMENT '下载地址',
  `is_push` tinyint(1) DEFAULT '0' COMMENT '0:不需要；1:需要推送',
  `is_binding` tinyint(1) DEFAULT '0' COMMENT '0:不捆绑；1:捆绑',
  `size` varchar(255) DEFAULT NULL COMMENT '包大小',
  `created_at` timestamp NULL DEFAULT '1990-01-01 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `package` WRITE;
/*!40000 ALTER TABLE `package` DISABLE KEYS */;

INSERT INTO `package` (`id`, `pid`, `user_id`, `name`, `icon`, `version`, `buddle_id`, `apple_id`, `ipa_url`, `certificate_url`, `certificate_id`, `introduction`, `download_num`, `download_url`, `is_push`, `is_binding`, `size`, `created_at`, `updated_at`)
VALUES
	(2,0,10,'secretalbum','http://www.677677.club/storage/5dd355988a82f.jpg','v1.1','com.siyan.secretalbum',NULL,'http://www.677677.club/storage/5dccb60f7e6da.ipa',NULL,NULL,'23232323444',5,NULL,0,0,'2.3M','2019-11-19 02:38:16','2019-11-25 16:27:13'),
	(3,0,10,'rings','http://www.677677.club/storage/5dcb9bab42546.jpg','v1.1','com.weliao1.weliao1',NULL,'http://www.677677.club/storage/5dd1ee6bed1bb.ipa',NULL,NULL,'Rings',0,NULL,1,0,'34M','2019-11-19 01:36:52','2019-11-20 14:18:50'),
	(4,0,10,'fazhi',NULL,'v1.1','com.siyan.secretalbum',NULL,'http://49.235.90.84:8893/storage/5dc25fe331903.ipa',NULL,NULL,'222',0,NULL,0,0,NULL,'2019-11-06 14:02:06','2019-11-20 14:18:51'),
	(5,0,10,'ccc',NULL,'v1.1','com.weliao1.weliao1',NULL,'http://49.235.90.84:8893/storage/5dc27a405325a.ipa',NULL,NULL,'ddd',0,NULL,0,0,NULL,'2019-11-06 15:46:08','2019-11-20 14:18:53'),
	(6,0,10,'DaBaoCeShi',NULL,'1.0.0','com.hudapang.DaBaoCeShi',NULL,'http://49.235.90.84:8893/storage/5dc27f435b249.ipa',NULL,NULL,'空包测试',0,NULL,0,0,NULL,'2019-11-06 16:07:31','2019-11-20 14:18:55'),
	(7,0,10,'DaBaoCeShi2',NULL,'1.0.0','com.hudapang.DaBaoCeShi',NULL,'http://49.235.90.84:8893/storage/5dc28a5512372.ipa',NULL,NULL,'空包测试添加icon和启动图',0,NULL,0,0,NULL,'2019-11-06 08:55:48','2019-11-20 14:18:57'),
	(8,0,10,'Rings','http://677677.club/storage/5dd3e984e9f2d.gif','1.0.4','com.zhb4.zhb4',5,'http://www.677677.club/storage/5dd36124f279f.ipa',NULL,NULL,'ssss2',0,NULL,1,0,NULL,'2019-11-19 21:09:24','2019-11-20 14:18:58'),
	(9,0,11,'testbag1','http://www.677677.club/storage/5ddb87b778c91.png','1.0.2','com.dfc.xiaoshuo3333',7,'http://www.677677.club/storage/5ddba9041f687.ipa',NULL,NULL,'第一个测试包',24,NULL,1,1,NULL,'2019-11-26 07:33:22','2019-11-27 09:47:39'),
	(15,9,11,'222',NULL,'12','com.siyan.secretalbum',7,'http://www.677677.club/storage/5ddcbcb7d6852.ipa',NULL,NULL,'323',8,NULL,0,0,NULL,'2019-11-26 07:30:48','2019-11-27 09:47:57'),
	(17,9,11,'捆绑包01','http://www.677677.club/storage/5ddce3f0b07ec.png','1.0.0','AA.pangpang.zhushou01',7,'http://www.677677.club/storage/5ddd0560d0a91.ipa',NULL,NULL,'第一个被捆绑包',15,NULL,1,0,NULL,'2019-11-26 22:01:58','2019-11-27 09:48:15');

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
  `role` tinyint(1) DEFAULT '1' COMMENT '1:普通用户（自己提供开发者账号）；2:普通用户（我们提供开发者账号）；3：开发者；-9:超级管理员',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `packnum` int(4) DEFAULT NULL COMMENT '最大上传包的数量',
  `udidnum` int(4) DEFAULT NULL COMMENT '最大新增udid数量',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `users_email_unique` (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `productids`, `origin_password`, `role`, `created_at`, `updated_at`, `packnum`, `udidnum`)
VALUES
	(1,'admin','admin@admin.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','Qycn4fg2B9twOpltVX48ubWcMIsb4buZhZim5MNUMOQn9GvJaeHsaWxQD0rP','1,2,3,4,9,10,11,12,13,14,15,17,19,21,22,100,1000,1002,1004,1006,1008,1010,1012,1014,1100,1102,1104,1106,1108,1109,1111,1113,1115,1116,1118,1120,1122,1126,1128,1130,1132,1134,1136,1138,1141,1143,1145,1146,1148,1150,1152,1156,1158,1159,1161,1163,1164,1166,1167,1169,1170,1171,1173,1175,1177,1179,1180,1182,1183,1185','123456',-9,'2016-07-07 07:38:31','2019-03-18 11:24:58',NULL,NULL),
	(2,'yh001','yh001@yh001.com','$2y$10$HbFv7qKtZTo2Qk2/VYy0dOi7RHxTMp..FE8L9qgQSQchYu2u10AWa','aBfhoyZtDN2xfD0dEmJGBSrmylDTBcIXmJxyTorG3JZuVZBOYYL2PtXOdVjp',NULL,'1234567',1,'2019-11-20 06:35:53','2019-11-20 06:35:53',NULL,NULL),
	(3,'yh002','yh002@yh002.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','UmfQuXNyY9m1m69udOQculVYFD1EKZyueI9rCpb3exlTsdkWQ2OjkzDd1OB4','1,2,3,4,9,10,11,12,13,14,15,17,19,21,22,100,1000,1002,1004,1006,1008,1010,1012,1014,1100,1102,1104,1106,1108,1109,1111,1113,1115,1116,1118,1120,1122,1126,1128,1130,1132,1134,1136,1138,1141,1143,1145,1146,1148,1150,1152,1156,1158,1159,1161,1163,1164,1166,1167,1169,1170,1171,1173,1175,1177,1179,1180,1182,1183,1185','123456',1,'2019-03-13 10:12:30','2019-04-02 17:33:36',NULL,NULL),
	(4,'yh003','yh003@yh003.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','cl1KJGH4hbnYJaRMdnhfbulnXobGqXjncr0ndGn6Ma0mbSsBbvnP2r2tS4nz',NULL,'123456',1,'2019-03-18 11:24:45','2019-04-02 13:31:47',NULL,NULL),
	(5,'jim','Jim.Chuang@huntsman.com.tw','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','e6YZk7mAx02UF5alV6tRWgySTmkX4vFAMKA7NEvw6lF95MmXBEL3I0QpNMSi',NULL,'123456',1,'2019-03-27 18:54:37','2019-04-01 14:12:41',NULL,NULL),
	(6,'yh004','yh004@yh004.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','wUQoBM377E89H5KuQ13mI1uEjbPERwHALw7Q8nypG7YGTdKsI69CTisbypch',NULL,'123456',1,'2019-03-28 09:25:15','2019-04-02 10:35:28',NULL,NULL),
	(7,'admin001','admin001@admin001.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','y8AlPEjI5Ws884hZFXTbjh8uwHg3c2OkzOOHr9uac6DqeDA6DZtsG3SmdzY7',NULL,'123456',1,'2019-03-28 10:31:32','2019-04-02 11:33:23',NULL,NULL),
	(8,'gusest','gusest@com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','tvzWdCjajeDt8hC6y3lq1EthToH9LQT49E3BvpHMv7h3jl82lDRWa3hDYFSX',NULL,'123456',1,'2019-03-28 11:06:12','2019-03-29 11:43:02',NULL,NULL),
	(9,'guanliyuan001@guanliyuan001.com','guanliyuan001@guanliyuan001.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','umg3H5VaYN39vjKszTnl4Hx79h8J95h87BTveNqjJ5iuywkHCQsGJyMb57F4',NULL,'123456',1,'2019-03-29 17:40:06','2019-04-04 21:08:45',NULL,NULL),
	(10,'pedro','bard@bard.com','$2y$10$bq7ANiJ/pWrsGDX3tWuRfub3lDbWR7vVhpTxMLcyon3o3PM2XvYLG','KwvyQGvpBgqW5ib6z7PlcB4Rfa6u91eo6yCZsl3uJFaEna386rCV7dykqun8',NULL,'123456',1,'2019-04-01 14:09:28','2019-04-02 10:41:12',NULL,NULL),
	(11,'test1','example@example.com-1574647518','$2y$10$ClyGVQYzQrgRun146FGjZuthi2P8835Aa2VmjuuV.i9D.obZWbKzm','XBb3hqJxjhbJ6hMt3xhnvRsSyU4m3eJ2XUhDEvKl0LYZjhMczRFjz0KrjMXn',NULL,'test1',1,'2019-11-25 10:05:18',NULL,1,1000);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
