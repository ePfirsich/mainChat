# mainChat-Datenbank für MySQL
#
#

ALTER TABLE `chat` DROP `c_br`;
ALTER TABLE `chat` CHANGE `c_id` `c_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `online` CHANGE `o_id` `o_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

DROP TABLE `sequence`;
ALTER TABLE `online` DROP `o_chat_id`;
ALTER TABLE `forum_beitraege` DROP `po_gesperrt`;
ALTER TABLE `user` DROP `u_sicherer_modus`;
ALTER TABLE `user` DROP `u_zeilen`;
ALTER TABLE `online` DROP `o_timestamp`;

ALTER TABLE `user` ADD `u_nachrichten_empfangen` int(1) UNSIGNED NOT NULL DEFAULT 1;

ALTER TABLE `forum_kategorien` CHANGE `fo_id` `kat_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `forum_kategorien` CHANGE `fo_name` `kat_name` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `forum_kategorien` CHANGE `fo_order` `kat_order` SMALLINT(5) NOT NULL DEFAULT '0';
ALTER TABLE `forum_kategorien` CHANGE `fo_admin` `kat_admin` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `forum_foren` CHANGE `th_id` `forum_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `forum_foren` CHANGE `th_fo_id` `forum_kategorie_id` SMALLINT(5) NOT NULL DEFAULT '0';
ALTER TABLE `forum_foren` CHANGE `th_name` `forum_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `forum_foren` CHANGE `th_desc` `forum_beschreibung` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `forum_foren` CHANGE `th_anzthreads` `forum_anzahl_themen` MEDIUMINT(8) NOT NULL DEFAULT '0';
ALTER TABLE `forum_foren` CHANGE `th_anzreplys` `forum_anzahl_antworten` MEDIUMINT(8) NOT NULL DEFAULT '0';
ALTER TABLE `forum_foren` CHANGE `th_postings` `forum_beitraege` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `forum_foren` CHANGE `th_order` `forum_order` SMALLINT(5) NOT NULL DEFAULT '0';

ALTER TABLE `forum_beitraege` CHANGE `po_id` `beitrag_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT; 
ALTER TABLE `forum_beitraege` CHANGE `po_th_id` `beitrag_forum_id` INT(10) NOT NULL DEFAULT '0'; 
ALTER TABLE `forum_beitraege` CHANGE `po_u_id` `beitrag_user_id` INT(11) NOT NULL DEFAULT '0'; 
ALTER TABLE `forum_beitraege` CHANGE `po_threadorder` `beitrag_order` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL; 
ALTER TABLE `forum_beitraege` CHANGE `po_ts` `beitrag_thema_timestamp` BIGINT(14) NOT NULL DEFAULT '0'; 
ALTER TABLE `forum_beitraege` CHANGE `po_threadts` `beitrag_antwort_timestamp` BIGINT(14) NOT NULL DEFAULT '0'; 
ALTER TABLE `forum_beitraege` CHANGE `po_vater_id` `beitrag_thema_id` INT(10) NOT NULL DEFAULT '0'; 
ALTER TABLE `forum_beitraege` CHANGE `po_titel` `beitrag_titel` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''; 
ALTER TABLE `forum_beitraege` CHANGE `po_text` `beitrag_text` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL; 
ALTER TABLE `forum_beitraege` CHANGE `po_threadgesperrt` `beitrag_gesperrt` INT(1) UNSIGNED NOT NULL DEFAULT '0'; 
ALTER TABLE `forum_beitraege` CHANGE `po_topposting` `beitrag_angepinnt` INT(1) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `forum_beitraege` RENAME INDEX `po_th_id` TO `beitrag_forum_id`;
ALTER TABLE `forum_beitraege` RENAME INDEX `po_u_id` TO `beitrag_user_id`;
ALTER TABLE `forum_beitraege` RENAME INDEX `po_vater_id` TO `beitrag_thema_id`;
ALTER TABLE `forum_beitraege` RENAME INDEX `po_themasort` TO `beitrag_themasort`;


ALTER TABLE `forum_foren` RENAME INDEX `th_fo_id` TO `forum_kategorie_id`;
ALTER TABLE `forum_foren` RENAME INDEX `th_name` TO `forum_name`;
