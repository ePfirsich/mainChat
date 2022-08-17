<?php
require_once("functions/functions.php");
umask(700);
set_time_limit(120);

$title = $body_titel;
zeige_header($title, 0);

echo "<body>";

// Verzeichnis für Logs definieren
if (strlen($log) == 0) {
	$log = "logs";
}

// Verzeichnis für Logs ggf anlegen
if (!file_exists($log)) {
	mkdir($log, 0700);
}

// Chat expire und Kopie in Log für alle öffentlichen Zeilen, die älter als 15 Minuten sind
echo "Expire Chattexte:<br>";
$query = pdoQuery("SELECT *, `r_name` FROM `chat` LEFT JOIN `raum` ON `c_raum` = `r_id` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`c_zeit`)) > 1900 AND `c_typ` != 'P' ORDER BY `c_raum`, `c_id`", []);

$resultCount = $query->rowCount();
if ($resultCount > 0) {
	$i = 0;
	$loesche = "";
	$raum_alt = "---ohne---";
	$result = $query->fetchAll();
	foreach($result as $zaehler => $row) {
		set_time_limit(20);
		
		if ($i > 0) {
			$loesche .= ",";
		}
		if (!$row['r_name']) {
			$row['r_name'] = "_ohne_Raum";
		}
		
		// Chat-Zeile ins Log schreiben
		$r_name = $log . "/" . str_replace("/", "_", $row['r_name']);
		if ($raum_alt != $r_name) {
			echo " Raum $r_name<br>";
		}
		
		// Ggf. Log routieren, falls > 100 MB
		if (is_file($r_name) && filesize($r_name) > 100000000) {
			rename($r_name, $r_name . "_" . date("dmY"));
		}
		
		$handle = @fopen($r_name, "a");
		if ($handle && $handle != -1) {
			$text = "[" . $row['c_zeit'] . "][" . $row['c_typ'] . "]";
			if (strlen($row['c_von_user']) > 0) {
				$text = $text . "[" . $row['c_von_user'] . "]";
			}
			$text = $text . " " . $row['c_text'];
			fputs($handle, "$text<br>");
			fclose($handle);
			@chmod($r_name, 0700);
			echo "Chat-Zeile ins Log schreiben...<br>";
			echo "<br>";
		} else {
			echo "<b>Fehler:</b> Kann Logdatei '$r_name' nicht öffnen!<br>";
		}
		
		$loesche .= $row['c_id'];
		$raum_alt = $r_name;
		$i++;
	}
	
	// Chat-Zeilen löschen
	pdoQuery("DELETE FROM `chat` WHERE `c_id` IN (:c_id)", [':c_id'=>$loesche]);
}

if ($expire_privat) {
	// Chat expire und Kopie in Log für alle privaten Zeilen, die älter als 15 Minuten sind
	$query = pdoQuery("SELECT SQL_BUFFER_RESULT * FROM `chat` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`c_zeit`)) > 900  AND c_typ = 'P' ORDER BY `c_raum`, `c_id`", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			set_time_limit(20);
			
			// Chat-Zeile in Log schreiben
			$r_name = $log . "/chat_privatnachrichten";
			
			// Ggf. Log routieren, falls > 100 MB
			if (filesize($r_name) > 100000000) {
				rename($r_name, $r_name . "_" . date("dmY"));
			}
			
			$handle = fopen($r_name, "a");
			if ($handle != -1) {
				$text = "[" . $row['c_zeit'] . "][" . $row['c_typ'] . "]";
				if (strlen($row['c_von_user']) > 0) {
					$text = $text . "[" . $row['c_von_user'] . "]";
				}
				$text = $text . " " . $row['c_text'];
				fputs($handle, "$text<br>");
				fclose($handle);
				chmod($r_name, 0700);
			} else {
				echo "<b>Fehler:</b> Kann Logdatei '" . $log . "/chat_privatnachrichten' nicht öffnen!<br>";
			}
			
			// Chat-Zeile löschen
			pdoQuery("DELETE FROM `chat` WHERE `c_id` = :c_id", [':c_id'=>$row['c_id']]);
		}
	}
} else {
	// Chat expire für alle privaten Zeilen, die älter als 15 Minuten sind
	pdoQuery("DELETE FROM `chat` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`c_zeit`)) > 900 AND `c_typ` = 'P' OR `c_zeit` = '' OR `c_zeit` = 0", []);
}

set_time_limit(120);

// Alle Systemnachrichten löschen
// Alle leeren Nachrichten löschen
pdoQuery("DELETE FROM `chat` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`c_zeit`)) > 900 AND `c_typ` = 'S' OR `c_text` = '' OR `c_text` IS NULL OR `c_zeit` = '' OR `c_zeit` IS NULL", []);

// Benutzer ausloggen, wenn $timeout überschritten wurde
echo "Benutzer ausloggen...<br>";
$query = pdoQuery("SELECT SQL_BUFFER_RESULT `o_user`, `o_raum`, `o_id`, `o_who`, `o_name` FROM `online` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) > :timeout", [':timeout'=>$timeout]);

$resultCount = $query->rowCount();
if ($resultCount > 1) {
	$result = $query->fetchAll();
	foreach($result as $zaehler => $row) {
		// Chat verlassen und Nachricht an alle Benutzer im aktuellen Raum schreiben
		echo "Folgender Benutzer wurde ausgeloggt: " . $row['o_name'] . "<br>";
		
		// Aus dem Chat ausloggen
		ausloggen($row['o_user'], $row['o_name'], $row['o_raum'], $row['o_id']);
	}
}
echo "<br>";

// Gäste löschen, falls ausgeloggt und älter als 4 Minuten
echo "Gäste löschen<br>";
$query = pdoQuery("SELECT SQL_BUFFER_RESULT `u_id` FROM `user` LEFT JOIN `online` ON `o_user` = `u_id` WHERE `u_level` = 'G' AND `o_id` IS NULL AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`u_login`)) > 240", []);

$resultCount = $query->rowCount();
if ($resultCount) {
	$result = $query->fetchAll();
	foreach($result as $zaehler => $row) {
		// Benutzer löschen
		pdoQuery("DELETE FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$row['u_id']]);
		
		// Benutzer-Ignore Einträge löschen
		pdoQuery("DELETE FROM `iignore` WHERE `i_user_aktiv` = :i_user_aktiv OR `i_user_passiv` = :i_user_passiv", [':i_user_aktiv'=>$row['u_id'], ':i_user_passiv'=>$row['u_id']]);
		
		// Gesperrte Räume löschen
		pdoQuery("DELETE FROM `sperre` WHERE `s_user` = :s_user", [':s_user'=>$row['u_id']]);
	}
}

// Leere temporäre Räume löschen, deren Besitzer nicht online ist
echo "Leere temporäre Räume löschen<br>";
$query = pdoQuery("SELECT `r_id`, `r_name`, `r_besitzer` FROM `raum` LEFT JOIN `online` ON `o_user` = `r_besitzer` WHERE `r_status2` = 'T'", []);

$resultCount = $query->rowCount();
if ($resultCount > 0) {
	$result = $query->fetchAll();
	foreach($result as $zaehler => $row) {
		$query2 = pdoQuery("SELECT `o_id` FROM `online` WHERE `o_user` = :o_user", [':o_user'=>$row['r_besitzer']]);
		$result2Count = $query2->rowCount();
		
		// Ist der Besitzer noch online?
		if($result2Count == 0) {
			// Ist der Raum leer?
			$query3 = pdoQuery("SELECT `o_id` FROM `raum`, `online` WHERE `r_id` = :r_id AND `o_raum` = `r_id`", [':r_id'=>$row['r_id']]);
			$result3Count = $query3->rowCount();
			if ($result3Count == 0) {
				// Raum löschen
				pdoQuery("DELETE FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>$row['r_id']]);
				
				// Gesperrte Räume löschen
				pdoQuery("DELETE FROM `sperre` WHERE `s_raum` = :s_raum", [':s_raum'=>$row['r_id']]);
			}
		}
	}
}


// Lösche alle Nachrichten, die älter als $mailloescheauspapierkorb Tage sind
echo " mail ";
flush();
// Löscht alle Nachrichten, die älter als $mailloescheauspapierkorb Tage sind
if ($mailloescheauspapierkorb < 1) {
	$mailloescheauspapierkorb = 14;
}
pdoQuery("DELETE FROM `mail` WHERE `m_status` = 'geloescht' AND `m_geloescht_ts` < :m_geloescht_ts", [':m_geloescht_ts'=>date("YmdHis", mktime(0, 0, 0, date("m"), date("d") - intval($mailloescheauspapierkorb), date("Y")))]);

// Gast aus der Tabelle online löschen
pdoQuery("DELETE FROM `online` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) > :timeout", [':timeout'=>($timeout*4)]);



// Täglicher expire, um  03:10 Uhr
$zeit = date("H:i");
#$zeit="03:10"; #DEBUG
if ($zeit == "03:10") {
	echo "Täglicher expire<br>";
	
	if (!isset($usernamen_expire) || $usernamen_expire == 0) {
		$usernamen_expire = 15724800;
	}
	// Benutzer löschen, die länger als $usernamen_expire (config.php default =26 Wochen (=15724800 Sekunden)) nicht online waren
	// und nicht auf gesperrt stehen (=Z) und kein Superuser sind (=S)
	echo "<br><b>Expire:</b> Benutzer ";
	flush();
	
	// Baut den Query, für nicht mehr Expire RB, um
	$besitzer = "";
	$query = pdoQuery("SELECT DISTINCT `r_besitzer` FROM `raum` WHERE `r_status2` = 'P' ORDER BY `r_besitzer`", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			if (strlen($besitzer) > 0) {
				$besitzer .= ", " . $row['r_besitzer'];
			} else {
				$besitzer .= $row['r_besitzer'];
			}
		}
		if (strlen($besitzer) > 0) {
			$besitzer = " AND u_id NOT IN (" . $besitzer . ")";
		}
	}
	if (strlen($besitzer) > 0) {
		$query = pdoQuery("SELECT SQL_BUFFER_RESULT `u_id`, `u_nick`, `u_login` FROM `user` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(u_login)) > "
			. $usernamen_expire . " AND `u_level` !='Z' AND `u_level` !='S' " . $besitzer, []);
	} else {
		$query = pdoQuery("SELECT SQL_BUFFER_RESULT `u_id`, `u_nick`, u_`login` FROM `user` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(u_login)) > "
			. $usernamen_expire . " AND `u_level` !='Z' AND `u_level` !='S'", []);
	}
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			// Zur sicherheit vor versehentlichem löschen
			if ($row['u_login'] == 0) {
				continue;
			}
			
			// Benutzer löschen
			pdoQuery("DELETE FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$row['u_id']]);
			
			// Benutzer-Ignore Einträge löschen
			pdoQuery("DELETE FROM iignore WHERE `i_user_aktiv` = :i_user_aktiv OR `i_user_passiv` = :i_user_passiv", [':i_user_aktiv'=>$row['u_id'], ':i_user_passiv'=>$row['u_id']]);
			
			// Gesperrte Räume löschen
			pdoQuery("DELETE FROM `sperre` WHERE `s_user` = :s_user", [':s_user'=>$row['u_id']]);
		}
	}
	
	// Lösche alle IP-Sperren, die älter als ein Tag sind
	echo " Sperren ";
	flush();
	pdoQuery("DELETE FROM `ip_sperre` WHERE now()-`is_zeit` > 36000 AND `is_warn` !='ja'", []);
	
	// Lösche alle Einträge in mail_check (E-Mail für Registrierung), die älter als 7 Tage sind
	echo " mail_check ";
	flush();
	pdoQuery("DELETE FROM `mail_check` WHERE DATE_ADD(datum,INTERVAL 7 DAY)<now()", []);
	
	// Alle invites löschen, für die es keine Benutzer mehr gibt.
	echo " Einladungen ";
	flush();
	$query = pdoQuery("SELECT SQL_BUFFER_RESULT `inv_id` FROM `invite` LEFT JOIN `user` ON `inv_user` = `u_id` WHERE `u_id` IS NULL", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			pdoQuery("DELETE FROM `invite` WHERE `inv_id` = :inv_id", [':inv_id'=>$row['inv_id']]);
		}
	}
	
	// alle invites löschen, für die es keine Räume mehr gibt.
	$query = pdoQuery("SELECT SQL_BUFFER_RESULT `inv_id` FROM `invite` LEFT JOIN `raum` ON `inv_raum` = `r_id` WHERE `r_id` IS NULL", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetch();
		foreach($result as $zaehler => $row) {
			pdoQuery("DELETE FROM `invite` WHERE `inv_id` = :inv_id", [':inv_id'=>$row['inv_id']]);
		}
	}
	
	// alle Bilder löschen, für die es keine Benutzer mehr gibt.
	echo " Bilder ";
	flush();
	$query = pdoQuery("SELECT SQL_BUFFER_RESULT `b_id` FROM `bild` LEFT JOIN `user` ON `b_user` = `u_id` WHERE `u_id` IS NULL", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetch();
		foreach($result as $zaehler => $row) {
			pdoQuery("DELETE FROM `bild` WHERE `b_id` = :b_id", [':b_id'=>$row['b_id']]);
		}
	}
	
	// alle Nachrichten löschen, für die es keine Benutzer mehr gibt.
	echo " Nachrichten ";
	flush();
	$query = pdoQuery("SELECT SQL_BUFFER_RESULT `m_id` FROM `mail` LEFT JOIN `user` ON `m_an_uid` = `u_id` WHERE `u_id` IS NULL", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			pdoQuery("DELETE FROM `mail` WHERE `m_id` = :m_id", [':m_id'=>$row['m_id']]);
		}
	}
	
	// alle Aktionen löschen, für die es keine Benutzer mehr gibt.
	echo " Aktionen ";
	flush();
	$query = pdoQuery("SELECT SQL_BUFFER_RESULT `a_id` FROM `aktion` LEFT JOIN `user` ON `a_user` = `u_id` WHERE `u_id` IS NULL", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			pdoQuery("DELETE FROM `aktion` WHERE `a_id` = :a_id", [':a_id'=>$row['a_id']]);
		}
	}
	
	// alle Profile löschen, für die es keine Benutzer mehr gibt.
	echo " Profile ";
	flush();
	$query = pdoQuery("SELECT SQL_BUFFER_RESULT `ui_id` FROM `userinfo` LEFT JOIN `user` ON `ui_userid` = `u_id` WHERE `u_id` IS NULL", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			pdoQuery("DELETE FROM `userinfo` WHERE `ui_id` = :ui_id", [':ui_id'=>$row['ui_id']]);
		}
	}
	
	// Alle Benutzer aus der Blacklist löschen, die es nicht mehr gibt.
	echo " Blacklist ";
	flush();
	$query = pdoQuery("SELECT SQL_BUFFER_RESULT `f_id` FROM `blacklist` LEFT JOIN `user` ON `f_blacklistid` = `u_id` WHERE `u_id` IS NULL", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetch();
		foreach($result as $zaehler => $row) {
			pdoQuery("DELETE FROM `blacklist` WHERE `f_id` = :f_id", [':f_id'=>$row['f_id']]);
		}
	}
	
	// Alle Ignore löschen, für die es keine Benutzer mehr gibt.
	echo " Ignore ";
	flush();
	$query = pdoQuery("SELECT SQL_BUFFER_RESULT `i_id` FROM `iignore` LEFT JOIN `user` ON `i_user_aktiv` = `u_id` WHERE `u_id` IS NULL", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetch();
		foreach($result as $zaehler => $row) {
			pdoQuery("DELETE FROM `iignore` WHERE `i_id` = :i_id", [':i_id'=>$row['i_id']]);
		}
	}
	$query = pdoQuery("SELECT SQL_BUFFER_RESULT `i_id` FROM `iignore` LEFT JOIN `user` ON `i_user_passiv` = `u_id` WHERE `u_id` IS NULL", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetch();
		foreach($result as $zaehler => $row) {
			pdoQuery("DELETE FROM `iignore` WHERE `i_id` = :i_id", [':i_id'=>$row['i_id']]);
		}
	}
	
	// Bereinigt die Spalte `u_gelesene_postings`
	$query = pdoQuery("SELECT `u_id` FROM `user`", []);
	$result = $query->fetchAll();
	foreach($result as $zaehler => $row) {
		$query = pdoQuery("SELECT `u_gelesene_postings`, `u_lastclean` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$row['u_id']]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$result = $query->fetch();
			$lastclean = $result['u_lastclean'];
			$gelesene = $result['u_gelesene_postings'];
			
			if ($lastclean == "0") {
				//keine Bereinignng nötig
				$lastclean = time();
				pdoQuery("UPDATE `user` SET `u_lastclean` = :u_lastclean WHERE `u_id` = :u_id", [':u_lastclean'=>$lastclean, ':u_id'=>$row['u_id']]);
			} else if ($lastclean < (time() - 2592000)) {
				//Bereinigung nötig
				$lastclean = time();
				$arr_gelesene = unserialize($gelesene);
				
				// Alle Beiträge in Feld einlesen
				$query = pdoQuery("SELECT `beitrag_id` FROM `forum_beitraege` ORDER BY `beitrag_id`", []);
				
				$result = $query->fetchAll();
				$arr_postings = array();
				foreach($result as $zaehler => $posting) {
					$arr_postings[] = $posting['beitrag_id'];
				}
				
				if (is_array($arr_gelesene)) {
					foreach($arr_gelesene as $k => $v) {
						$arr_gelesene[$k] = array_intersect($arr_gelesene[$k], $arr_postings);
					}
				}
				
				$gelesene_neu = serialize($arr_gelesene);
				
				pdoQuery("UPDATE `user` SET `u_lastclean` = :u_lastclean, `u_gelesene_postings` = :u_gelesene_postings WHERE `u_id` = :u_id", [':u_lastclean'=>$lastclean, ':u_gelesene_postings'=>$gelesene_neu, ':u_id'=>$row['u_id']]);
			}
		}
	}
	
	// Beiträge im Forum neu zählen
	echo " Beiträge im Forum neu zählen ";
	flush();
	bereinige_anz_in_thema();
	
	// Datenbank optimieren
	set_time_limit(600);
	echo " Optimieren ";
	flush();
	pdoQuery("OPTIMIZE TABLE `mail_check`, `top10cache`, `blacklist`, `forum_beitraege`, `forum_foren`, `aktion`, `bild`, `forum_kategorien`, `freunde`, `mail`, "
		. "`iignore`, `invite`, `ip_sperre`, `moderation`, `online`, `raum`, `sperre`, `user`, `userinfo`", []);
	sleep(60);
	set_time_limit(600);
	echo " Repariere user,raum ";
	flush();
	pdoQuery("REPAIR TABLE `user`, `raum`", []);
	sleep(60);
	echo " fertig<br>";
	flush();
}

/*****************************************************************************
 **																			**
 **						S T A T I S T I K E N								**
 **																			**
 *****************************************************************************/

/*****************************************************************************
 ** Anzahl der Benutzer die sich momentan im Chat befinden in die Statistik	**
 ** Datenbank eintragen.													**
 *****************************************************************************/

$currenttime = time();
$currentdate = date("Y-m-d H", $currenttime);

unset($onlinevhosts);

// Anzahl der Benutzer ermitteln, die online sind
$query = pdoQuery("SELECT COUNT(`o_id`) AS `anzahl` FROM `online`", []);

$resultCount = $query->rowCount();
if ($resultCount > 0) {
	$result = $query->fetch();
	$anzahl_online = $result['anzahl'];
} else {
	$anzahl_online = 0;
}

/* Die Anzahl der Benutzer im Chat/Forum wird nun in die Statistiken geschrieben. */
if ( isset($anzahl_online) && ($anzahl_online > 0) ) {
	$query = pdoQuery("SELECT SQL_BUFFER_RESULT `c_users` FROM `statistiken` WHERE DATE_FORMAT(`c_timestamp`,'%Y-%m-%d %H') = :c_timestamp ORDER BY `c_users` DESC", [':c_timestamp'=>$currentdate]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 0) {
		/* Es war noch kein Eintrag vorhanden. Neuer Eintrag wird angelegt. */
		pdoQuery("INSERT INTO `statistiken` (`c_timestamp`, `c_users`) VALUES (NOW(), :c_users)", [':c_users'=>$anzahl_online]);
	} else {
		/* Es war bereits ein Eintrag vorhanden. Die Anzahl der Benutzer wird erneuert wenn sie größer als der alte Wert  war. */
		$result = $query->fetch();
		$currentnr = $result['c_users'];
		
		if ($anzahl_online > $currentnr) {
			pdoQuery("UPDATE `statistiken` SET `c_users` = :c_users WHERE DATE_FORMAT(`c_timestamp`,'%Y-%m-%d %H') = :c_timestamp", [':c_users'=>$anzahl_online, ':c_timestamp'=>$currentdate]);
		}
	}
}

echo "fertig";
?>
</pre>
</body>
</html>