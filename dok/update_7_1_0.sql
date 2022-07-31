# mainChat-Datenbank für MySQL
#
#

ALTER TABLE `user` DROP `u_loginfehler`;
ALTER TABLE `user` CHANGE `u_login` `u_login` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP; 
ALTER TABLE `user` CHANGE `u_adminemail` `u_email` VARCHAR(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `user` DROP INDEX `u_adminemail`, ADD INDEX `u_email` (`u_email`) USING BTREE;
ALTER TABLE `user` ADD `u_passwort_code` VARCHAR(255) NULL DEFAULT NULL AFTER `u_passwort`;
ALTER TABLE `user` ADD `u_passwort_code_time` TIMESTAMP NULL DEFAULT NULL AFTER `u_passwort_code`; 
ALTER TABLE `user` ADD `u_email_neu` VARCHAR(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `user` ADD `u_email_code` VARCHAR(255) NULL DEFAULT NULL AFTER `u_email_neu`;
ALTER TABLE `user` ADD `u_passwortanforderung` TIMESTAMP NULL DEFAULT NULL AFTER `u_passwort_code_time`;
UPDATE `user` SET `u_passwort` = NULL;