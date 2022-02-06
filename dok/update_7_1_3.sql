# mainChat-Datenbank für MySQL
#
#

ALTER TABLE `online` DROP `o_chat_historie`;

CREATE TABLE `securitytokens` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`user_id` int(10) NOT NULL,
	`identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
	`securitytoken` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
	`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
