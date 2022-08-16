# mainChat-Datenbank für MySQL
#
#

ALTER TABLE `online` CHANGE `o_ip` `o_ip` VARCHAR(38) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';
