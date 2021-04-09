<?php

require("functions.php");
umask(700);
set_time_limit(120);

$title = $body_titel;
zeige_header_anfang($title, 'login');

zeige_header_ende();
?>
<body>
<pre>
<?php

// Verzeichnis für Logs definieren
if (strlen($log) == 0) :
	$log = "logs";
endif;

// Verzeichnis für Logs ggf anlegen
if (!file_exists($log)) :
	mkdir($log, 0700);
endif;

// Chat expire und Kopie in Log für alle öffentlichen Zeilen, die älter als 15 Minuten sind
echo "Expire Chattexte:\n";
$query = "SELECT *,r_name FROM chat LEFT JOIN raum ON c_raum=r_id "
	. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(c_zeit)) > 900 "
	. "AND c_typ!='P' " . "ORDER BY c_raum,c_id";
$result = mysqli_query($mysqli_link, $query);
if (!$result)
	echo mysqli_errno($mysqli_link) . " - " . mysqli_error($mysqli_link);

$rows = mysqli_num_rows($result);

if ($rows > 0) {
	$i = 0;
	$loesche = "";
	$raum_alt = "---ohne---";
	while ($i < $rows) {
		set_time_limit(20);
		$row = @mysqli_fetch_object($result);
		if ($i > 0)
			$loesche .= ",";
		if (!$row->r_name)
			$row->r_name = "_ohne_Raum";
		
		// Chat-Zeile in Log schreiben
		$r_name = $log . "/" . str_replace("/", "_", $row->r_name);
		if ($raum_alt != $r_name)
			echo "  Raum $r_name\n";
		
		// Ggf. Log routieren, falls > 100 MB
		if (is_file($r_name) && filesize($r_name) > 100000000)
			rename($r_name, $r_name . "_" . date("dmY"));
		
		$handle = @fopen($r_name, "a");
		if ($handle && $handle != -1) {
			$text = "[" . $row->c_zeit . "][" . $row->c_typ . "]";
			if (strlen($row->c_von_user) > 0) :
				$text = $text . "[" . $row->c_von_user . "]";
			endif;
			$text = $text . " " . $row->c_text;
			fputs($handle, "$text\n");
			fclose($handle);
			@chmod($r_name, 0700);
		} else {
			echo "<P><b>Fehler:</b> Kann Logdatei '$r_name' nicht öffnen!</P>\n";
		}
		
		$loesche .= "$row->c_id";
		$raum_alt = $r_name;
		$i++;
	}
	mysqli_free_result($result);
	
	// Chat-Zeilen löschen
	$query = "DELETE FROM chat WHERE c_id IN ($loesche)";
	$result3 = mysqli_query($mysqli_link, $query);
}

if ($expire_privat) {
	// Chat expire und Kopie in Log für alle privaten Zeilen, die älter als 15 Minuten sind
	$query = "SELECT SQL_BUFFER_RESULT * FROM chat "
		. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(c_zeit)) > 900 "
		. "AND c_typ='P' ORDER BY c_raum,c_id";
	
	$result = mysqli_query($mysqli_link, $query);
	$rows = mysqli_num_rows($result);
	
	if ($rows > 0) {
		$i = 0;
		while ($i < $rows) {
			set_time_limit(20);
			$row = @mysqli_fetch_object($result);
			
			// Chat-Zeile in Log schreiben
			$r_name = $log . "/chat_privatnachrichten";
			
			// Ggf. Log routieren, falls > 100 MB
			if (filesize($r_name) > 100000000)
				rename($r_name, $r_name . "_" . date("dmY"));
			
			$handle = fopen($r_name, "a");
			if ($handle != -1) {
				$text = "[" . $row->c_zeit . "][" . $row->c_typ . "]";
				if (strlen($row->c_von_user) > 0) {
					$text = $text . "[" . $row->c_von_user . "]";
				}
				$text = $text . " " . $row->c_text;
				fputs($handle, "$text\n");
				fclose($handle);
				chmod($r_name, 0700);
			} else {
				echo "<P><b>Fehler:</b> Kann Logdatei '" . $log
					. "/chat_privatnachrichten' nicht öffnen!</P>\n";
			}
			
			// Chat-Zeile löschen
			$result3 = mysqli_query($mysqli_link, "DELETE FROM chat WHERE c_id=$row->c_id");
			$i++;
		}
		mysqli_free_result($result);
	}
	
} else {
	// Chat expire für alle privaten Zeilen, die älter als 15 Minuten sind
	$query = "DELETE FROM chat "
		. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(c_zeit)) > 900 "
		. "AND c_typ='P' OR c_zeit='' OR c_zeit=0";
	$result = mysqli_query($mysqli_link, $query);
}

set_time_limit(120);

// Alle Systemnachrichten löschen
// Alle leeren Nachrichten löschen
$query = "DELETE FROM chat "
	. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(c_zeit)) > 900 "
	. "AND c_typ='S' OR c_text='' OR c_text IS NULL OR c_zeit='' OR c_zeit IS NULL";
$result = mysqli_query($mysqli_link, $query);

// User ausloggen, wenn $timeout überschritten wurde
echo "User ausloggen...";
$query = "SELECT SQL_BUFFER_RESULT o_user,o_raum,o_id,o_who,o_name FROM online "
	. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) > $timeout";

$result = mysqli_query($mysqli_link, $query);
if ($result) {
	while ($rows = mysqli_fetch_object($result)) {
		// Chat verlassen und Nachricht an alle User im aktuellen Raum schreiben
		verlasse_chat($rows->o_user, $rows->o_name, $rows->o_raum);
		logout($rows->o_id, $rows->o_user, "expire->timeout $timeout");
		echo "$rows->o_name ";
	}
	mysqli_free_result($result);
}
echo "\n";

// Gast-User löschen, falls ausgelogt und älter als 4 Minuten
echo "Gäste löschen\n";
$query = "SELECT SQL_BUFFER_RESULT u_id FROM user left join online on o_user=u_id "
	. "WHERE u_level='G' AND o_id IS NULL "
	. "AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(u_login)) > 240";
$result = mysqli_query($mysqli_link, $query);
if ($result && mysqli_num_rows($result) > 0) {
	while ($row = mysqli_fetch_object($result)) {
		// User löschen
		$result3 = mysqli_query($mysqli_link, "DELETE FROM `user` WHERE u_id=$row->u_id");
		
		// User-Ignore Einträge löschen
		$result3 = mysqli_query($mysqli_link, 
			"DELETE FROM iignore WHERE i_user_aktiv=$row->u_id OR i_user_passiv=$row->u_id");
		
		// Gesperrte Räume löschen
		$query3 = "DELETE FROM sperre WHERE s_user=$row->u_id";
		$result3 = mysqli_query($mysqli_link, $query3);
	}
}
@mysqli_free_result($result);

// Leere temporäre Räume löschen, deren Besitzer nicht online ist
echo "Leere temporäre Räume löschen\n";
$query = "SELECT SQL_BUFFER_RESULT r_id,r_name FROM raum "
	. "LEFT JOIN online ON o_user=r_besitzer "
	. "WHERE r_status2 LIKE 'T' AND o_timestamp IS NULL";

$result = mysqli_query($mysqli_link, $query);
if ($result && mysqli_num_rows($result) > 0) {
	while ($row = mysqli_fetch_object($result)) {
		// Ist der Raum leer?
		$query = "SELECT o_id from raum,online "
			. "WHERE r_id=$row->r_id AND o_raum=r_id";
		$result3 = mysqli_query($mysqli_link, $query);
		if ($result3) {
			if (mysqli_num_rows($result3) == 0) {
				// Raum löschen
				$query = "DELETE FROM raum WHERE r_id=$row->r_id";
				$result4 = mysqli_query($mysqli_link, $query);
				
				// Gesperrte Räume löschen
				$query = "DELETE FROM sperre WHERE s_raum=$row->r_id";
				$result4 = mysqli_query($mysqli_link, $query);
			}
			mysqli_free_result($result3);
		}
	}
	mysqli_free_result($result);
}

// Täglicher expire, um  03:10 Uhr
$zeit = date("H:i");
#$zeit="03:10"; #DEBUG
if ($zeit == "03:10") {
	
	echo "Täglicher expire\n";
	
	if (!isset($nicknamen_expire) || $nicknamen_expire == 0) {
		$nicknamen_expire = 15724800;
	}
	// User löschen, die länger als $nicknamen_expire (config.php default =26 Wochen (=15724800 Sekunden)) nicht online waren
	// und nicht auf gesperrt stehen (=Z) und kein Superuser sind (=S)
	echo "<br><b>Expire:</b> User ";
	flush();
	
	// Baut den Query, für nicht mehr Expire RB, um
	$besitzer = "";
	$query = "SELECT DISTINCT r_besitzer FROM raum WHERE r_status2 = 'P' ORDER BY r_besitzer";
	$result = mysqli_query($mysqli_link, $query);
	if ($result) {
		while ($row = mysqli_fetch_object($result)) {
			if (strlen($besitzer) > 0) {
				$besitzer .= ", " . $row->r_besitzer;
			} else {
				$besitzer .= $row->r_besitzer;
			}
		}
		if (strlen($besitzer) > 0) {
			$besitzer = " AND u_id NOT IN (" . $besitzer . ")";
		}
	}
	if (strlen($besitzer) > 0) {
		$query = "SELECT SQL_BUFFER_RESULT `u_id`, `u_nick`, `u_login` FROM `user` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(u_login)) > "
			. $nicknamen_expire . " " . " AND `u_level` !='Z' AND `u_level` !='S' "
			. $besitzer;
	} else {
		$query = "SELECT SQL_BUFFER_RESULT `u_id`, `u_nick`, u_`login` FROM `user` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(u_login)) > "
			. $nicknamen_expire . " " . " AND `u_level` !='Z' AND `u_level` !='S'";
	}
	
	// Orginalquery ohne Raumbesitzer Expire wird nimmer benötigt
	// $query="SELECT SQL_BUFFER_RESULT u_id FROM `user` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(u_login)) > 15724800 AND `u_level` !='Z' AND `u_level` !='S'";
	
	$result = mysqli_query($mysqli_link, $query);
	if ($result) {
		while ($row = mysqli_fetch_object($result)) {
			
			// zur sicherheit vor versehentlichem löschen
			if ($row->u_login == 0) {
				continue;
			}
			
			// User löschen
			$result3 = mysqli_query($mysqli_link, "DELETE FROM `user` WHERE `u_id`=$row->u_id");
			
			// User-Ignore Einträge löschen
			$result3 = mysqli_query($mysqli_link, 
				"DELETE FROM iignore WHERE `i_user_aktiv`=$row->u_id OR `i_user_passiv`=$row->u_id");
			
			// Gesperrte Räume löschen
			$query3 = "DELETE FROM `sperre` WHERE `s_user`=$row->u_id";
			$result3 = mysqli_query($mysqli_link, $query3);
			$i++;
		}
	}
	@mysqli_free_result($result);
	
	// Lösche alle IP-Sperren, die älter als ein Tag sind
	echo " Sperren ";
	flush();
	$query = "DELETE from ip_sperre where now()-is_zeit > 36000 AND is_warn!='ja'";
	$result = mysqli_query($mysqli_link, $query);
	
	// Lösche alle Einträge in mail_check (E-Mail für Registrierung), die älter als 7 Tage sind
	echo " mail_check ";
	flush();
	$query = "DELETE from mail_check where DATE_ADD(datum,INTERVAL 7 DAY)<now()";
	$result = mysqli_query($mysqli_link, $query);
	
	// alle invites löschen, für die es keine User mehr gibt.
	echo " Einladungen ";
	flush();
	$query = "SELECT SQL_BUFFER_RESULT inv_id FROM invite LEFT JOIN user on inv_user=u_id WHERE u_id IS NULL";
	$result = mysqli_query($mysqli_link, $query);
	if ($result)
		while ($row = mysqli_fetch_object($result))
			$result3 = mysqli_query($mysqli_link, 
				"DELETE FROM invite WHERE inv_id=" . $row->inv_id);
	mysqli_free_result($result);
	
	// alle invites löschen, für die es keine Räume mehr gibt.
	$query = "SELECT SQL_BUFFER_RESULT inv_id FROM invite LEFT JOIN raum on inv_raum=r_id WHERE r_id IS NULL";
	$result = mysqli_query($mysqli_link, $query);
	if ($result)
		while ($row = mysqli_fetch_object($result))
			$result3 = mysqli_query($mysqli_link, 
				"DELETE FROM invite WHERE inv_id=" . $row->inv_id);
	mysqli_free_result($result);
	
	// alle Bilder löschen, für die es keine User mehr gibt.
	echo " Bilder ";
	flush();
	$query = "SELECT SQL_BUFFER_RESULT b_id FROM bild LEFT JOIN user on b_user=u_id WHERE u_id IS NULL";
	$result = mysqli_query($mysqli_link, $query);
	if ($result)
		while ($row = mysqli_fetch_object($result))
			$result3 = mysqli_query($mysqli_link, 
				"DELETE FROM bild WHERE b_id=" . $row->b_id);
	mysqli_free_result($result);
	
	// alle Mails löschen, für die es keine User mehr gibt.
	echo " Mails ";
	flush();
	$query = "SELECT SQL_BUFFER_RESULT m_id FROM mail LEFT JOIN user on m_an_uid=u_id WHERE u_id IS NULL";
	$result = mysqli_query($mysqli_link, $query);
	if ($result)
		while ($row = mysqli_fetch_object($result))
			$result3 = mysqli_query($mysqli_link, 
				"DELETE FROM mail WHERE m_id=" . $row->m_id);
	mysqli_free_result($result);
	
	// alle Aktionen löschen, für die es keine User mehr gibt.
	echo " Aktionen ";
	flush();
	$query = "SELECT SQL_BUFFER_RESULT a_id FROM aktion LEFT JOIN user on a_user=u_id WHERE u_id IS NULL";
	$result = mysqli_query($mysqli_link, $query);
	if ($result)
		while ($row = mysqli_fetch_object($result))
			$result3 = mysqli_query($mysqli_link, 
				"DELETE FROM aktion WHERE a_id=" . $row->a_id);
	mysqli_free_result($result);
	
	// alle Profile löschen, für die es keine User mehr gibt.
	echo " Profile ";
	flush();
	$query = "SELECT SQL_BUFFER_RESULT ui_id FROM userinfo LEFT JOIN user on ui_userid=u_id WHERE u_id IS NULL";
	$result = mysqli_query($mysqli_link, $query);
	if ($result)
		while ($row = mysqli_fetch_object($result))
			$result3 = mysqli_query($mysqli_link, 
				"DELETE FROM userinfo WHERE ui_id=" . $row->ui_id);
	mysqli_free_result($result);
	
	// alle User aus der Blacklist löschen, die es nicht mehr gibt.
	echo " Blacklist ";
	flush();
	$query = "SELECT SQL_BUFFER_RESULT f_id FROM blacklist LEFT JOIN user on f_blacklistid=u_id WHERE u_id IS NULL;";
	$result = mysqli_query($mysqli_link, $query);
	if ($result)
		while ($row = mysqli_fetch_object($result))
			$result3 = mysqli_query($mysqli_link, 
				"DELETE FROM blacklist WHERE f_id=" . $row->f_id);
	mysqli_free_result($result);
	
	// alle Ignore löschen, für die es keine User mehr gibt.
	echo " Ignore ";
	flush();
	$query = "SELECT SQL_BUFFER_RESULT i_id FROM iignore LEFT JOIN user on i_user_aktiv=u_id WHERE u_id IS NULL";
	$result = mysqli_query($mysqli_link, $query);
	if ($result)
		while ($row = mysqli_fetch_object($result))
			$result3 = mysqli_query($mysqli_link, 
				"DELETE FROM iignore WHERE i_id=" . $row->i_id);
	mysqli_free_result($result);
	$query = "SELECT SQL_BUFFER_RESULT i_id FROM iignore LEFT JOIN user on i_user_passiv=u_id WHERE u_id IS NULL";
	$result = mysqli_query($mysqli_link, $query);
	if ($result)
		while ($row = mysqli_fetch_object($result))
			$result3 = mysqli_query($mysqli_link, 
				"DELETE FROM iignore WHERE i_id=" . $row->i_id);
	mysqli_free_result($result);
	
	// Datenbank optimieren
	set_time_limit(600);
	echo " Optimieren ";
	flush();
	$query = "OPTIMIZE TABLE mail_check,top10cache,blacklist,posting,thema,aktion,bild,forum,freunde,mail,"
		. "iignore,invite,ip_sperre,moderation,online,raum,sperre,user,userinfo,sequence";
	mysqli_query($mysqli_link, $query);
	sleep(60);
	set_time_limit(600);
	echo " Repariere user,raum ";
	flush();
	$query = "REPAIR TABLE user,raum";
	mysqli_query($mysqli_link, $query);
	sleep(60);
	echo " fertig<br>";
	flush();
}
;

/*************************************************************************
 **																		**
 **				S T A T I S T I K E N								**
 **																		**
 *************************************************************************/

/*************************************************************************
 ** Anzahl der User die sich momentan im Chat befinden in die Statistik	**
 ** Datenbank eintragen.												**
 *************************************************************************/

if ((strlen($STAT_DB_HOST) > 0)) {
	mysqli_connect('p:'.$mysqlhost, $mysqluser, $mysqlpass, $dbase);
	mysqli_set_charset($mysqli_link, "utf8mb4");
	
	$currenttime = time();
	$currentdate = date("Y-m-d H", $currenttime);
	
	unset($onlinevhosts);
	
	/* Als erstes werden die verschiedenen Virtuellen Hosts ermittelt.
	// bzw. gesammt gespeichert wenn $STAT_DB_COLLECT gesetzt ist */
	
	if (isset($STAT_DB_COLLECT) && strlen($STAT_DB_COLLECT) > 0) {
		$r0 = @mysqli_query($mysqli_link, "SELECT '$STAT_DB_COLLECT'");
	} else {
		$r0 = @mysqli_query($mysqli_link, 
			"SELECT SQL_BUFFER_RESULT DISTINCT o_vhost FROM online WHERE o_raum>0");
	}
	
	if ($r0 > 0) {
		$i = 0;
		$n = @mysqli_num_rows($r0);
		
		if ($n > 0) {
			/* Für jeden Virtuellen Host werden die Anzahl der User im Chat	*/
			/* aus der OnlineDB geholt. */
			
			while ($i < $n) {
				
				if (isset($STAT_DB_COLLECT) && strlen($STAT_DB_COLLECT) > 0) {
					$o_vhost = $STAT_DB_COLLECT;
					$r1 = @mysqli_query($mysqli_link, 
						"SELECT COUNT(o_id) AS anzahl FROM online ");
				} else {
					$o_vhost = @mysqli_result($r0, $i, "o_vhost");
					$r1 = @mysqli_query($mysqli_link, 
						"SELECT COUNT(o_id) AS anzahl FROM online WHERE o_vhost='"
							. mysqli_real_escape_string($mysqli_link, $o_vhost) . "' ");
				}
				
				if ($r1 > 0) {
					$onlinevhosts["$o_vhost"] = intval(
						@mysqli_result($r1, 0, "anzahl"));
				}
				
				$i++;
			}
		}
	}
	
	/* Die Anzahl der User im Chat für jeden Virtuellen Host wird	*/
	/* nun in die StatisticDB geschrieben. */
	
	if ((isset($onlinevhosts)) && (count($onlinevhosts) > 0)) {
		if ($dbase != $STAT_DB_HOST) {
			$mysqli_link2 = mysqli_connect('p:'.$STAT_DB_HOST, $STAT_DB_USER, $STAT_DB_PASS, $STAT_DB_NAME);
			mysqli_set_charset($mysqli_link, "utf8mb4");
		} else {
			$mysqli_link2 = $mysqli_link;
		}
		
		if (!$mysqli_link2) {
			echo "<b>Fehler: Keine Verbindung zum SQL Server für die Statistik</b><br>\n";
		} else {
			
			mysqli_select_db($mysqli_link2, $STAT_DB_NAME);
			
			reset($onlinevhosts);
			
			while (list($name, $nr) = each($onlinevhosts)) {
				$r0 = mysqli_query($mysqli_link2, 
					"SELECT SQL_BUFFER_RESULT c_users FROM chat WHERE DATE_FORMAT(c_timestamp,'%Y-%m-%d %H') = '$currentdate' AND c_host='"
						. mysqli_real_escape_string($mysqli_link, $name) . "' ORDER BY c_users DESC");
				
				if ($r0) {
					$n = @mysqli_num_rows($r0);
					if ($n == 0) {
						/* Es war noch kein Eintrag vorhanden. Neuer Eintrag wird angelegt. */
						@mysqli_query($mysqli_link2, 
							"INSERT INTO chat (c_timestamp, c_users, c_host) VALUES (NOW(),$nr,'"
								. mysqli_real_escape_string($mysqli_link, $name) . "')");
					} else {
						/* Es war bereits ein Eintrag vorhanden. Die Anzahl der	*/
						/* User wird erneuert wenn sie größer als der alte Wert	*/
						/* war. */
						
						$currentnr = @mysqli_result($r0, 0, "c_users");
						
						if ($nr > $currentnr) {
							@mysqli_query($mysqli_link2, 
								"UPDATE chat SET c_users=$nr WHERE DATE_FORMAT(c_timestamp,'%Y-%m-%d %H') = '$currentdate' AND c_host='"
									. mysqli_real_escape_string($mysqli_link, $name) . "'");
						}
					}
				}
			}
		}
	}
}

echo "fertig\n";
?>
</pre>
</body>
</html>