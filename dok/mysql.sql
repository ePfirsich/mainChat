# mainChat-Datenbank für MySQL
#
#

# Tabelle user 		Userdaten, Level
# u_id 						ID
# u_nick					Temp. Benutzername im Chat
# u_passwort				Passwort
# u_level					Userlevel A-Z
# u_neu						Zeitpunkt der Erstanmeldung
# u_login					Zeitpunkt des letzten Logins
# u_farbe					Voreingestellte Farbe
# u_cookie					Falls 1 -> Cookie erwünscht
# u_auth					Normalerweise unbelegt, legt die Authentisierung (Cookies, HTTP-Auth) für den User fest
# u_email					E-Mail Adresse für Administatrion, muss angegeben werden
# u_ip_historie 			Liste der IP-Adressen der letzten Logins
# u_away					away-Text des Benutzers
# u_smilies					Benutzer will Smilie-Grafiken sehen
# u_agb						User hat Nutzungsbestimmungen akzeptiert
# u_punkte_gesamt			Gesamtpunktzahl des Users
# u_punkte_monat			Punktzahl des aktuellen Monats
# u_punkte_jahr				Punktzahl des aktuellen Jahrs
# u_punkte_datum_monat		Monat, auf die sich u_punkte_monat bezieht
# u_punkte_datum_jahr		Jahr, auf die sich u_punkte_datum_jahr bezieht
# u_punkte_gruppe			Grafik-Symbol für die Punktegruppe
# u_gelesene_postings		ID der gelesenen Beiträge im Forum
# u_chathomepage			Chathomepage freigeschalten
# u_eintritt				Eintrittstext
# u_austritt				Austrittstext
# u_signatur				Signatur für Mail und Forum
# u_lastclean				Zeitpunkt der letzten bereinigung von u_gelesene_postings
# u_nick_historie			Nickwechsel Historie
# u_profil_historie			Admin-Änderungen am Profil-Historie
# u_kommentar				Kommentar, warum ein User gesperrt wurde
# u_punkte_anzeigen			User will seinen PunkteWürfel zeigen
# u_systemmeldungen			User will System Login/Logout Nachrichten sehen
# u_knebel					Speichert die Restknebelzeit falls vorhanden

# Tabelle chat 		Chat-Nachrichten
# c_id				ID
# c_von_user		Nachricht ist von User u_nick
# c_von_user_id		ID c_von_user
# c_an_user 		Nachricht ist an User u_id
# c_typ 			Art der Nachricht (privat, System, normal)
# c_raum 			Zeiger auf r_id
# c_text 			Chatzeile
# c_zeit 			Zeitstempel
# c_farbe			Farbe der Textzeile
#					normal oder letzte => Zeilenumbruch in dieser Chatzeile

# Tabelle moderation	moderierte Chat-Nachrichten
# c_id				ID
# c_von_user		Nachricht ist von User Benutzername
# c_von_user_id		ID c_von_user
# c_an_user 		Nachricht ist an User u_id
# c_typ 			Art der Nachricht (privat, System, normal) ("N"= chatnachricht, "P"=vordefinierte Antwort)
# c_raum 			Zeiger auf r_id
# c_text 			Chatzeile
# c_zeit 			Zeitstempel
# c_farbe			Farbe der Textzeile
# c_moderator		Id des Moderators, der diese Nachricht bearbeitet.

# Tabelle raum	Beschreibung eines Raums
# r_id 			ID
# r_name 		Name des Raums
# r_eintritt 	Eintrittstext
# r_austritt 	Austrittstext
# r_status1 	Status1 (offen, geschlossen, moderiert)
# r_besitzer 	Besitzer des Raums -> u_id
# r_topic 		Titel des Raums
# r_status2 	Status2 (permanent, temporär)
# r_smilie		Smilies erlaubt
# r_min_punkte	Anzahl der Punkte die ein User haben muss, bevor er den Raum betreten kann

# Tabelle online	welcher User ist in welchem Raum online
# o_id int 		ID
# o_user 		User -> u_id
# o_raum 		Raum -> r_id
# o_hash 		Hash aus IP-Adresse, User
# o_ip 			IP von der zugegriffen wurde
# o_who 		Bereich in der Community, in der sich der User befindet
#				0=Chat, 1=Login, 2=Forum
# o_aktiv 		Wann war der User zuletzt aktiv
# o_browser 	Verwendeter Browser
# o_knebel		zeitstempel, wie lange ein user geknebelt ist
# o_http_stuff	Komprimierte HTTP-Headerinformationen
# o_http_stuff2	Headerinformationen Feld 2
# o_userdata	Komprimierte Userdaten aus Tabelle user
# o_userdata2	Userdaten Feld 2
# o_userdata3	Userdaten Feld 3
# o_userdata4	Userdaten Feld 4
# o_ignore		Komprimierte Daten aus Tabelle ignore
# o_level 		Aktueller Level des Users aus Tabelle user
# o_login		Zeitpunkt des Logins
# o_punkte		Aktuelle Anzahl der Punkte des Users
# o_aktion		Zeitpunkt, wann die letzte Aktion ausgeführt wurde
# o_timeout_zeit	Zeitpunkt, wann die letzte Zeile im Chat geschrieben wurde
# o_timeout_warnung	1=Warnung über bevorstehenden Logout wurde angezeigt
# o_spam_zeit		Sekundengenauer Zeitpunkt (timestamp), aus den sich o_spam_zeilen und o_spam_byte bezieht
# o_spam_zeilen		Enthält serialised die Anzahl der Zeilen pro Sekunde, die der User zuletzt schrieb
# o_spam_byte		Enthält serialised die Anzahl der Bytes/Zeichen pro Sekunde, die der User zuletzt schrieb
# o_dicecheck		Welcher Dicecheck vom User aktiviert ist

# Tabelle sperre	welcher User ist in welchem Raum für wie lange gesperrt
# s_id int 		ID
# s_user 		User -> u_id
# s_raum 		Raum -> r_id
# s_zeit 		Zeitpunkt wann Datensatz angelegt wurde

# Tabelle statistiken	welcher User ist in welchem Raum für wie lange gesperrt
# id int 		ID
# c_users 		Anzahl der User
# c_timestamp 		Zeitstempel

# Tabelle iignore	welcher User hat welchen anderen user stummgeschaltet
# i_id 			ID
# i_user_aktiv 		User -> u_id
# i_user_passiv 	User -> u_id

# Tabelle ip_sperre	komplette Sperre für User von bestimmten IPs
# is_id 		ID
# is_ip 		IP-Adresse als xxx.yyy.zzz
# is_ip_byte 		Anzahl der Bytes in is_ip
# is_domain 		Domainname oder Domain+Host
# is_zeit 		Zeitpunkt wann Datensatz angelegt wurde
# is_owner		Wer diesen Datensatz zuletzt geschrieben hat
# is_infotext		Text mit dem Grund der Sperre
# is_warn		ja=Eintrag löst nur Warnmeldung aus / nein=Eintrag sperrt IP oder Netz



# CREATE DATABASE mainchat CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 05. Feb 2022 um 15:34
-- Server-Version: 10.3.32-MariaDB-0ubuntu0.20.04.1
-- PHP-Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `mainchat_chat`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `aktion`
--

CREATE TABLE `aktion` (
	`a_id` int(11) UNSIGNED NOT NULL,
	`a_user` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`a_wann` enum('Sofort/Offline','Sofort/Online','Login','Alle 5 Minuten') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Login',
	`a_was` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`a_wie` set('keine','Chat-Mail','E-Mail','OLM') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'keine',
	`a_zeit` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bild`
--

CREATE TABLE `bild` (
	`b_id` int(11) UNSIGNED NOT NULL,
	`b_user` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`b_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`b_bild` blob NOT NULL,
	`b_mime` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`b_width` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`b_height` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blacklist`
--

CREATE TABLE `blacklist` (
	`f_id` int(11) UNSIGNED NOT NULL,
	`f_userid` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`f_blacklistid` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`f_zeit` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`f_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat`
--

CREATE TABLE `chat` (
	`c_id` int(11) UNSIGNED NOT NULL,
	`c_von_user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`c_an_user` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`c_typ` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
	`c_raum` int(11) NOT NULL DEFAULT 0,
	`c_text` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`c_zeit` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`c_farbe` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`c_von_user_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`c_gelesen` int(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `forum_beitraege`
--

CREATE TABLE `forum_beitraege` (
	`beitrag_id` int(10) UNSIGNED NOT NULL,
	`beitrag_forum_id` int(10) NOT NULL DEFAULT 0,
	`beitrag_user_id` int(11) NOT NULL DEFAULT 0,
	`beitrag_thema_id` int(10) NOT NULL DEFAULT 0,
	`beitrag_thema_timestamp` bigint(14) NOT NULL DEFAULT 0,
	`beitrag_antwort_timestamp` bigint(14) NOT NULL DEFAULT 0,
	`beitrag_titel` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`beitrag_text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
	`beitrag_gesperrt` int(1) UNSIGNED NOT NULL DEFAULT 0,
	`beitrag_angepinnt` int(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `forum_foren`
--

CREATE TABLE `forum_foren` (
	`forum_id` int(10) UNSIGNED NOT NULL,
	`forum_kategorie_id` smallint(5) NOT NULL DEFAULT 0,
	`forum_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`forum_beschreibung` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`forum_anzahl_themen` mediumint(8) NOT NULL DEFAULT 0,
	`forum_anzahl_antworten` mediumint(8) NOT NULL DEFAULT 0,
	`forum_beitraege` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`forum_order` smallint(5) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `forum_kategorien`
--

CREATE TABLE `forum_kategorien` (
	`kat_id` smallint(5) UNSIGNED NOT NULL,
	`kat_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`kat_order` smallint(5) NOT NULL DEFAULT 0,
	`kat_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `freunde`
--

CREATE TABLE `freunde` (
	`f_id` int(11) UNSIGNED NOT NULL,
	`f_userid` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`f_freundid` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`f_zeit` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`f_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`f_status` enum('beworben','bestaetigt') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bestaetigt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `iignore`
--

CREATE TABLE `iignore` (
	`i_id` int(11) UNSIGNED NOT NULL,
	`i_user_aktiv` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`i_user_passiv` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `invite`
--

CREATE TABLE `invite` (
	`inv_id` int(11) NOT NULL,
	`inv_raum` int(11) DEFAULT NULL,
	`inv_user` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ip_sperre`
--

CREATE TABLE `ip_sperre` (
	`is_id` int(11) UNSIGNED NOT NULL,
	`is_ip` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`is_domain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`is_zeit` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`is_ip_byte` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`is_owner` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`is_infotext` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`is_warn` enum('ja','nein') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nein'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mail`
--

CREATE TABLE `mail` (
	`m_id` int(11) UNSIGNED NOT NULL,
	`m_status` enum('neu','gelesen','geloescht','neu/verschickt') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'neu',
	`m_von_uid` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`m_an_uid` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`m_zeit` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`m_geloescht_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`m_betreff` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`m_text` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mail_check`
--

CREATE TABLE `mail_check` (
	`email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`datum` date NOT NULL DEFAULT '0000-00-00',
	`u_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `moderation`
--

CREATE TABLE `moderation` (
	`c_id` int(11) UNSIGNED NOT NULL,
	`c_von_user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`c_an_user` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`c_typ` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`c_raum` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`c_text` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`c_zeit` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`c_farbe` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`c_von_user_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`c_moderator` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `online`
--

CREATE TABLE `online` (
  `o_id` int(11) UNSIGNED NOT NULL,
  `o_user` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `o_raum` int(11) NOT NULL DEFAULT 0,
  `o_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_ip` varchar(38) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_who` smallint(3) UNSIGNED DEFAULT NULL,
  `o_aktiv` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `o_browser` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_knebel` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `o_http_stuff` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_http_stuff2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_userdata` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_userdata2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_userdata3` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_userdata4` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_level` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_ignore` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `o_punkte` int(11) NOT NULL DEFAULT 0,
  `o_aktion` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `o_timeout_zeit` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `o_spam_zeilen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_spam_byte` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_spam_zeit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_dicecheck` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `o_timeout_warnung` int(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `raum`
--

CREATE TABLE `raum` (
	`r_id` int(11) UNSIGNED NOT NULL,
	`r_name` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`r_eintritt` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`r_austritt` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`r_status1` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`r_besitzer` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`r_topic` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`r_status2` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`r_min_punkte` int(10) UNSIGNED NOT NULL DEFAULT 0,
	`r_smilie` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `securitytokens`
--

CREATE TABLE `securitytokens` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`user_id` int(10) NOT NULL,
	`identifier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
	`securitytoken` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
	`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sperre`
--

CREATE TABLE `sperre` (
	`s_id` int(11) UNSIGNED NOT NULL,
	`s_user` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`s_raum` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`s_zeit` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `statistiken`
--

CREATE TABLE `statistiken` (
	`c_users` int(11) NOT NULL DEFAULT 0,
	`c_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `top10cache`
--

CREATE TABLE `top10cache` (
	`t_id` int(11) UNSIGNED NOT NULL,
	`t_zeit` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`t_eintrag` int(11) UNSIGNED NOT NULL DEFAULT 1,
	`t_daten` mediumblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
	`u_id` int(11) UNSIGNED NOT NULL,
	`u_neu` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`u_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
	`u_auth` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`u_nick` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`u_passwort` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`u_passwort_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`u_passwort_code_time` timestamp NULL DEFAULT NULL,
	`u_email` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`u_email_neu` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`u_email_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`u_passwortanforderung` timestamp NULL DEFAULT NULL,
	`u_level` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'U',
	`u_farbe` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`u_away` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`u_ip_historie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`u_punkte_gesamt` int(10) UNSIGNED NOT NULL DEFAULT 0,
	`u_punkte_monat` int(10) UNSIGNED NOT NULL DEFAULT 0,
	`u_punkte_jahr` int(10) UNSIGNED NOT NULL DEFAULT 0,
	`u_punkte_datum_monat` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '00',
	`u_punkte_datum_jahr` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0000',
	`u_punkte_gruppe` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`u_gelesene_postings` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`u_eintritt` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`u_austritt` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`u_signatur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`u_lastclean` bigint(14) NOT NULL DEFAULT 0,
	`u_nick_historie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`u_profil_historie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`u_kommentar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`u_knebel` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`u_avatare_anzeigen` int(1) UNSIGNED NOT NULL DEFAULT 1,
	`u_layout_farbe` int(1) UNSIGNED NOT NULL DEFAULT 0,
	`u_layout_chat_darstellung` int(1) UNSIGNED NOT NULL DEFAULT 1,
	`u_systemmeldungen` int(1) UNSIGNED NOT NULL DEFAULT 1,
	`u_smilies` int(1) UNSIGNED NOT NULL DEFAULT 1,
	`u_punkte_anzeigen` int(1) UNSIGNED NOT NULL DEFAULT 1,
	`u_chathomepage` int(1) UNSIGNED NOT NULL DEFAULT 0,
	`u_agb` int(1) UNSIGNED NOT NULL DEFAULT 0,
	`u_emails_akzeptieren` int(1) UNSIGNED NOT NULL DEFAULT 1,
	`u_nachrichten_empfangen` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci PACK_KEYS=1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userinfo`
--

CREATE TABLE `userinfo` (
	`ui_id` int(11) NOT NULL,
	`ui_userid` int(11) NOT NULL DEFAULT 0,
	`ui_geburt` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`ui_beruf` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`ui_hobby` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
	`ui_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
	`ui_wohnort` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
	`ui_geschlecht` int(1) UNSIGNED NOT NULL DEFAULT 0,
	`ui_beziehungsstatus` int(1) UNSIGNED NOT NULL DEFAULT 0,
	`ui_typ` int(1) UNSIGNED NOT NULL DEFAULT 0,
	`ui_lieblingsfilm` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
	`ui_lieblingsserie` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
	`ui_lieblingsbuch` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
	`ui_lieblingsschauspieler` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
	`ui_lieblingsgetraenk` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
	`ui_lieblingsgericht` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
	`ui_lieblingsspiel` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
	`ui_lieblingsfarbe` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
	`ui_homepage` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
	`ui_hintergrundfarbe` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`ui_ueberschriften_textfarbe` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`ui_ueberschriften_hintergrundfarbe` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`ui_inhalt_textfarbe` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`ui_inhalt_linkfarbe` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`ui_inhalt_linkfarbe_aktiv` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`ui_inhalt_hintergrundfarbe` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `aktion`
--
ALTER TABLE `aktion`
	ADD PRIMARY KEY (`a_id`),
	ADD KEY `a_user` (`a_user`),
	ADD KEY `a_wann` (`a_wann`);

--
-- Indizes für die Tabelle `bild`
--
ALTER TABLE `bild`
	ADD PRIMARY KEY (`b_id`),
	ADD KEY `b_user` (`b_user`);

--
-- Indizes für die Tabelle `blacklist`
--
ALTER TABLE `blacklist`
	ADD PRIMARY KEY (`f_id`),
	ADD KEY `f_blacklistid` (`f_blacklistid`);

--
-- Indizes für die Tabelle `chat`
--
ALTER TABLE `chat`
	ADD PRIMARY KEY (`c_id`),
	ADD KEY `c_an_user` (`c_an_user`),
	ADD KEY `c_raum` (`c_raum`);

--
-- Indizes für die Tabelle `forum_beitraege`
--
ALTER TABLE `forum_beitraege`
	ADD PRIMARY KEY (`beitrag_id`),
	ADD KEY `beitrag_forum_id` (`beitrag_forum_id`),
	ADD KEY `beitrag_user_id` (`beitrag_user_id`),
	ADD KEY `beitrag_thema_id` (`beitrag_thema_id`),
	ADD KEY `beitrag_themasort` (`beitrag_forum_id`,`beitrag_thema_id`,`beitrag_antwort_timestamp`,`beitrag_thema_timestamp`);

--
-- Indizes für die Tabelle `forum_foren`
--
ALTER TABLE `forum_foren`
	ADD PRIMARY KEY (`forum_id`),
	ADD KEY `forum_kategorie_id` (`forum_kategorie_id`),
	ADD KEY `forum_name` (`forum_name`);

--
-- Indizes für die Tabelle `forum_kategorien`
--
ALTER TABLE `forum_kategorien`
	ADD PRIMARY KEY (`kat_id`);

--
-- Indizes für die Tabelle `freunde`
--
ALTER TABLE `freunde`
	ADD PRIMARY KEY (`f_id`),
	ADD KEY `f_userid` (`f_userid`),
	ADD KEY `f_freundid` (`f_freundid`);

--
-- Indizes für die Tabelle `iignore`
--
ALTER TABLE `iignore`
	ADD PRIMARY KEY (`i_id`),
	ADD KEY `i_user_aktiv` (`i_user_aktiv`),
	ADD KEY `i_user_passiv` (`i_user_passiv`);

--
-- Indizes für die Tabelle `invite`
--
ALTER TABLE `invite`
	ADD PRIMARY KEY (`inv_id`),
	ADD KEY `inv_user` (`inv_user`);

--
-- Indizes für die Tabelle `ip_sperre`
--
ALTER TABLE `ip_sperre`
	ADD PRIMARY KEY (`is_id`),
	ADD KEY `is_owner` (`is_owner`),
	ADD KEY `is_warn` (`is_warn`);

--
-- Indizes für die Tabelle `mail`
--
ALTER TABLE `mail`
	ADD PRIMARY KEY (`m_id`),
	ADD KEY `m_status` (`m_status`),
	ADD KEY `m_von_uid` (`m_von_uid`),
	ADD KEY `m_an_uid` (`m_an_uid`),
	ADD KEY `m_geloescht_ts` (`m_an_uid`,`m_status`,`m_geloescht_ts`);

--
-- Indizes für die Tabelle `mail_check`
--
ALTER TABLE `mail_check`
	ADD PRIMARY KEY (`email`);

--
-- Indizes für die Tabelle `moderation`
--
ALTER TABLE `moderation`
	ADD PRIMARY KEY (`c_id`),
	ADD KEY `c_an_user` (`c_an_user`),
	ADD KEY `c_raum` (`c_raum`),
	ADD KEY `c_von_user_id` (`c_von_user_id`),
	ADD KEY `c_moderator` (`c_moderator`);

--
-- Indizes für die Tabelle `online`
--
ALTER TABLE `online`
	ADD PRIMARY KEY (`o_id`),
	ADD UNIQUE KEY `o_user` (`o_user`),
	ADD UNIQUE KEY `o_name` (`o_name`),
	ADD KEY `o_raum` (`o_raum`),
	ADD KEY `o_aktiv` (`o_aktiv`),
	ADD KEY `o_hash` (`o_hash`(250)),
	ADD KEY `o_ip` (`o_ip`),
	ADD KEY `o_browser` (`o_browser`(250)),
	ADD KEY `o_level` (`o_level`);

--
-- AUTO_INCREMENT für Tabelle `online`
--
ALTER TABLE `online` MODIFY `o_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Indizes für die Tabelle `raum`
--
ALTER TABLE `raum`
	ADD PRIMARY KEY (`r_id`),
	ADD KEY `r_besitzer` (`r_besitzer`),
	ADD KEY `r_status1` (`r_status1`),
	ADD KEY `r_status2` (`r_status2`);

--
-- Indizes für die Tabelle `sperre`
--
ALTER TABLE `sperre`
	ADD PRIMARY KEY (`s_id`),
	ADD KEY `s_user` (`s_user`),
	ADD KEY `s_raum` (`s_raum`);

--
-- Indizes für die Tabelle `statistiken`
--
ALTER TABLE `statistiken`
	ADD PRIMARY KEY (`id`),
	ADD KEY `c_users` (`c_users`);

--
-- Indizes für die Tabelle `top10cache`
--
ALTER TABLE `top10cache`
	ADD PRIMARY KEY (`t_id`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
	ADD PRIMARY KEY (`u_id`),
	ADD UNIQUE KEY `u_nick` (`u_nick`),
	ADD KEY `u_level` (`u_level`),
	ADD KEY `u_email` (`u_email`) USING BTREE;
ALTER TABLE `user` ADD FULLTEXT KEY `u_ip_historie` (`u_ip_historie`);

--
-- Indizes für die Tabelle `userinfo`
--
ALTER TABLE `userinfo`
	ADD PRIMARY KEY (`ui_id`),
	ADD KEY `ui_userid` (`ui_userid`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `aktion`
--
ALTER TABLE `aktion`
	MODIFY `a_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `bild`
--
ALTER TABLE `bild`
	MODIFY `b_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `blacklist`
--
ALTER TABLE `blacklist`
	MODIFY `f_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `chat`
--
ALTER TABLE `chat`
	MODIFY `c_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `forum_beitraege`
--
ALTER TABLE `forum_beitraege`
	MODIFY `beitrag_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `forum_foren`
--
ALTER TABLE `forum_foren`
	MODIFY `forum_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `forum_kategorien`
--
ALTER TABLE `forum_kategorien`
	MODIFY `kat_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `freunde`
--
ALTER TABLE `freunde`
	MODIFY `f_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `iignore`
--
ALTER TABLE `iignore`
	MODIFY `i_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `invite`
--
ALTER TABLE `invite`
	MODIFY `inv_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `ip_sperre`
--
ALTER TABLE `ip_sperre`
	MODIFY `is_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `mail`
--
ALTER TABLE `mail`
	MODIFY `m_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `moderation`
--
ALTER TABLE `moderation`
	MODIFY `c_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `raum`
--
ALTER TABLE `raum`
	MODIFY `r_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `sperre`
--
ALTER TABLE `sperre`
	MODIFY `s_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `statistiken`
--
ALTER TABLE `statistiken`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `top10cache`
--
ALTER TABLE `top10cache`
	MODIFY `t_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
	MODIFY `u_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `userinfo`
--
ALTER TABLE `userinfo`
	MODIFY `ui_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


INSERT INTO `raum` SET `r_name`='Lobby', `r_eintritt`='Hallöle', `r_topic`='Eingangshalle', `r_status1`='O', `r_status2`='P';