# mainChat-Datenbank für MySQL
#
#

DELETE FROM `aktion` WHERE `a_wie` = "SMS";
DELETE FROM `aktion` WHERE `a_wie` = "OLM,SMS";
DELETE FROM `aktion` WHERE `a_wie` = "E-Mail,SMS";
DELETE FROM `aktion` WHERE `a_wie` = "Chat-Mail,SMS";
ALTER TABLE `aktion` CHANGE `a_wie` `a_wie` SET('keine','Chat-Mail','E-Mail','OLM') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'keine';
ALTER TABLE `chat` CHANGE `c_raum` `c_raum` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `online` DROP `u_sms_extra`, DROP `o_vhost`;
DROP TABLE `sms`, `smsin`;
ALTER TABLE `user` DROP `u_sms_ok`, DROP `u_sms_guthaben`, DROP `u_sms_extra`, DROP `u_frames`, DROP `u_name`, DROP `u_backup`;
ALTER TABLE `user` CHANGE `u_gelesene_postings` `u_gelesene_postings` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `user` ADD `u_avatare_anzeigen` int(1) UNSIGNED NOT NULL DEFAULT 1;
ALTER TABLE `user` ADD `u_layout_farbe` int(1) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `user` ADD `u_layout_chat_darstellung` int(1) UNSIGNED NOT NULL DEFAULT 1;
ALTER TABLE `user` DROP `u_url`, DROP `u_farbe_alle`, DROP `u_farbe_noise`, DROP `u_farbe_priv`, DROP `u_farbe_bg`, DROP `u_farbe_sys`, DROP `u_clearedit`;
ALTER TABLE `chat` ADD `c_gelesen` int(1) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `userinfo` DROP `ui_ort`, DROP `ui_einstellungen`, `ui_geschlecht`, DROP `ui_beziehung`, DROP `ui_typ`, DROP `ui_tel`, DROP `ui_fax`, DROP `ui_handy`, DROP `ui_icq`, DROP `ui_strasse`, DROP `ui_plz`, DROP `ui_land`;
ALTER TABLE `userinfo` ADD `ui_wohnort` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL; 
ALTER TABLE `userinfo` ADD `ui_geschlecht` int(1) UNSIGNED NOT NULL DEFAULT 1;
ALTER TABLE `userinfo` ADD `ui_beziehungsstatus` int(1) UNSIGNED NOT NULL DEFAULT 1;
ALTER TABLE `userinfo` ADD `ui_typ` int(1) UNSIGNED NOT NULL DEFAULT 1;
ALTER TABLE `userinfo` ADD `ui_lieblingsfilm` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `userinfo` ADD `ui_lieblingsserie` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `userinfo` ADD `ui_lieblingsbuch` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `userinfo` ADD `ui_lieblingsschauspieler` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `userinfo` ADD `ui_lieblingsgetraenk` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `userinfo` ADD `ui_lieblingsgericht` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `userinfo` ADD `ui_lieblingsspiel` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `userinfo` ADD `ui_lieblingsfarbe` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `userinfo` ADD `ui_homepage` VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `user` DROP `u_systemmeldungen`;
ALTER TABLE `user` ADD `u_systemmeldungen` int(1) UNSIGNED NOT NULL DEFAULT 1;
ALTER TABLE `user` DROP `u_smilie`;
ALTER TABLE `user` ADD `u_smilies` int(1) UNSIGNED NOT NULL DEFAULT 1;
ALTER TABLE `user` DROP `u_punkte_anzeigen`;
ALTER TABLE `user` ADD `u_punkte_anzeigen` int(1) UNSIGNED NOT NULL DEFAULT 1;
ALTER TABLE `user` ADD `u_sicherer_modus` int(1) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `raum` DROP `r_werbung`;
DELETE FROM `raum` WHERE `r_status1` = "L";
ALTER TABLE `user` DROP `u_chathomepage`;
ALTER TABLE `user` ADD `u_chathomepage` int(1) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `user` DROP `u_agb`;
ALTER TABLE `user` ADD `u_agb` int(1) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `online` DROP `o_js`;
UPDATE `user` SET `u_agb` = 1;
ALTER TABLE `userinfo` ADD `ui_hintergrundfarbe` VARCHAR(6) NULL DEFAULT NULL;
ALTER TABLE `userinfo` ADD `ui_ueberschriften_textfarbe` VARCHAR(6) NULL DEFAULT NULL;
ALTER TABLE `userinfo` ADD `ui_ueberschriften_hintergrundfarbe` VARCHAR(6) NULL DEFAULT NULL;
ALTER TABLE `userinfo` ADD `ui_inhalt_textfarbe` VARCHAR(6) NULL DEFAULT NULL;
ALTER TABLE `userinfo` ADD `ui_inhalt_linkfarbe` VARCHAR(6) NULL DEFAULT NULL;
ALTER TABLE `userinfo` ADD `ui_inhalt_linkfarbe_aktiv` VARCHAR(6) NULL DEFAULT NULL;
ALTER TABLE `userinfo` ADD `ui_inhalt_hintergrundfarbe` VARCHAR(6) NULL DEFAULT NULL;
UPDATE `userinfo` SET `ui_hintergrundfarbe` = "ffffff";
UPDATE `userinfo` SET `ui_ueberschriften_textfarbe` = "ffffff";
UPDATE `userinfo` SET `ui_ueberschriften_hintergrundfarbe` = "007ABE";
UPDATE `userinfo` SET `ui_inhalt_textfarbe` = "000000";
UPDATE `userinfo` SET `ui_inhalt_linkfarbe` = "000000";
UPDATE `userinfo` SET `ui_inhalt_linkfarbe_aktiv` = "000000";
UPDATE `userinfo` SET `ui_inhalt_hintergrundfarbe` = "ffffff";
ALTER TABLE `userinfo` DROP `ui_farbe`;
ALTER TABLE `user` ADD `u_emails_akzeptieren` int(1) UNSIGNED NOT NULL DEFAULT 1;
ALTER TABLE `user` DROP `u_email`;


CREATE TABLE `statistiken` (
  `c_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `c_users` int(11) NOT NULL DEFAULT 0,
  `id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `statistiken` ADD PRIMARY KEY (`id`);
ALTER TABLE `statistiken` MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;