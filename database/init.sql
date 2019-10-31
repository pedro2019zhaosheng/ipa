CREATE TABLE `users` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`email`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`password`  varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`remember_token`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`created_at`  timestamp NULL DEFAULT NULL ,
`updated_at`  timestamp NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
UNIQUE INDEX `users_email_unique` (`email`) USING BTREE
);

CREATE TABLE `role` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`label`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`description`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`created_at`  timestamp NULL DEFAULT NULL ,
`updated_at`  timestamp NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
);

CREATE TABLE `user_role` (
`user_id`  int(10) UNSIGNED NOT NULL ,
`role_id`  int(10) UNSIGNED NOT NULL ,
PRIMARY KEY (`role_id`, `user_id`),
FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
INDEX `role_user_user_id_foreign` (`user_id`) USING BTREE
);

CREATE TABLE `permission` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`label`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`description`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  int(4) NOT NULL DEFAULT 1 COMMENT '系统日志是否需要记录该操作' ,
`created_at`  timestamp NULL DEFAULT NULL ,
`updated_at`  timestamp NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
);

CREATE TABLE `permission_role` (
`permission_id`  int(10) UNSIGNED NOT NULL ,
`role_id`  int(10) UNSIGNED NOT NULL ,
PRIMARY KEY (`permission_id`, `role_id`),
FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
INDEX `permission_role_role_id_foreign` (`role_id`) USING BTREE
);

CREATE TABLE `account` (
`id`  bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
`type`  tinyint(4) NOT NULL COMMENT '账号类型： 1-游客，2-手机号，3-qq, 4-微信，5-微博，6-百度，7-理发店虚拟账号' ,
`platform`  tinyint(4) NOT NULL COMMENT 'ios/android 1-ios 2-android' ,
`deviceno`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设备号' ,
`version`  varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '版本' ,
`model`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '机型' ,
`mobile`  bigint(20) NULL DEFAULT NULL COMMENT '手机号码' ,
`password`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '密码' ,
`token`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户登录token' ,
`social_id`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '第三方账号唯一码' ,
`avatar`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '头像' ,
`nickname`  varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '暂无昵称' COMMENT '昵称' ,
`reg_time`  bigint(20) NOT NULL COMMENT '注册时间' ,
`login_time`  bigint(20) NOT NULL COMMENT '最新一次登录时间' ,
`ip`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'ip地址' ,
`bind_mobile`  bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '绑定手机号' ,
`id_card`  char(18) NULL DEFAULT NULL COMMENT '身份证' ,
`lng`  decimal(20,14) NULL DEFAULT 0.00000000000000 COMMENT '经度' ,
`lat`  decimal(20,14) NULL DEFAULT 0.00000000000000 COMMENT '纬度' ,
PRIMARY KEY (`id`),
INDEX `i_type` (`type`) USING BTREE
);

CREATE TABLE `account_sms` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`mobile`  bigint(20) NOT NULL COMMENT '手机号' ,
`sms_code`  varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '验证码' ,
`sms_time`  bigint(20) NOT NULL COMMENT '验证码发送时间' ,
`ip`  varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL ,
PRIMARY KEY (`id`)
);

CREATE TABLE `y_banner` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`type`  tinyint(4) NOT NULL default 1 COMMENT '类型: 1-首页banner' ,
`title`  varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`pic`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '图片' ,
`url`  varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '跳转链接' ,
`platform`  tinyint(4) NULL DEFAULT NULL COMMENT '平台: 1-ios, 2-android' ,
`start_time`  bigint(20) NULL DEFAULT NULL COMMENT '开始时间' ,
`end_time`  bigint(20) NULL DEFAULT NULL COMMENT '结束时间' ,
`enable_time`  bigint(20) NULL DEFAULT NULL COMMENT '启用时间,和开始/结束时间作为2个时间检查点' ,
`sort`  int(10) NOT NULL COMMENT '顺序' ,
`status`  tinyint(4) NOT NULL COMMENT '状态' ,
`ctime`  bigint(20) NULL DEFAULT NULL ,
`utime`  bigint(20) NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
);

-- 消息
CREATE TABLE `y_news` (
`id`  bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
`to_user_id`  bigint(20) NULL DEFAULT NULL ,
`from_user_id`  bigint(20) NULL DEFAULT 0 COMMENT '谁操作的' ,
`action_type`  tinyint(50) NULL DEFAULT 0 COMMENT '操作类型: 1-评论，2-赞， 3-关注， 4-取消关注' ,
`content`  varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '操作内容' ,
`target_type`  tinyint(4) NULL DEFAULT 0 COMMENT '1-话题，2-动态' ,
`target_id`  bigint(20) NULL DEFAULT 0 COMMENT '话题/动态的id' ,
`read_flag`  tinyint(4) NULL DEFAULT 0 COMMENT '1--已读，0--未读，默认未读' ,
`ctime`  datetime NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
);
