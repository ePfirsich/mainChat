# mainChat-Datenbank für MySQL
#
#

ALTER TABLE `aktion` DROP `a_text`;

ALTER TABLE `raum` DROP `r_smilie`;
ALTER TABLE `raum` ADD `r_smilie` int(1) UNSIGNED NOT NULL DEFAULT 1;

ALTER TABLE `online` DROP `o_timeout_warnung`;
ALTER TABLE `online` ADD `o_timeout_warnung` int(1) UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `posting` DROP `po_gesperrt`;
ALTER TABLE `posting` ADD `po_gesperrt` int(1) UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `posting` DROP `po_threadgesperrt`;
ALTER TABLE `posting` ADD `po_threadgesperrt` int(1) UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `posting` DROP `po_topposting`;
ALTER TABLE `posting` ADD `po_topposting` int(1) UNSIGNED NOT NULL DEFAULT 0;