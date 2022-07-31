# mainChat-Datenbank für MySQL
#
#

ALTER TABLE `chat` engine = MyISAM;
ALTER TABLE `chat` CHANGE `c_text` `c_text` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE `aktion` engine = InnoDB;
ALTER TABLE `bild` engine = InnoDB;
ALTER TABLE `blacklist` engine = InnoDB;
ALTER TABLE `forum_beitraege` engine = InnoDB;
ALTER TABLE `forum_foren` engine = InnoDB;
ALTER TABLE `forum_kategorien` engine = InnoDB;
ALTER TABLE `freunde` engine = InnoDB;
ALTER TABLE `iignore` engine = InnoDB;
ALTER TABLE `ip_sperre` engine = InnoDB;
ALTER TABLE `mail` engine = InnoDB;
ALTER TABLE `mail_check` engine = InnoDB;
ALTER TABLE `moderation` engine = InnoDB;
ALTER TABLE `raum` engine = InnoDB;
ALTER TABLE `sperre` engine = InnoDB;
ALTER TABLE `top10cache` engine = InnoDB;
ALTER TABLE `user` engine = InnoDB;
ALTER TABLE `userinfo` engine = InnoDB;

ALTER TABLE `user` DROP `u_forum_postingproseite`;
