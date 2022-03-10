# mainChat-Datenbank für MySQL
#
#

ALTER TABLE `chat` DROP `c_br`;
ALTER TABLE `chat` CHANGE `c_id` `c_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `online` CHANGE `o_id` `o_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

DROP TABLE `sequence`;
ALTER TABLE `online` DROP `o_chat_id`;
ALTER TABLE `posting` DROP `po_gesperrt`;