# mainChat-Datenbank für MySQL
#
#

RENAME TABLE `forum` TO `forum_kategorien`;
RENAME TABLE `thema` TO `forum_foren`;
RENAME TABLE `posting` TO `forum_beitraege`;
ALTER TABLE `forum_beitraege` DROP `po_tiefe`;