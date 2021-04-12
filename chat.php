<?php

// chat.php muss mit id=$hash_id aufgerufen werden
// Optional kann $back als Trigger für die Ausgabe der letzten n-Zeilen angegeben werden

require("functions.php");

// Userdaten setzen
id_lese($id);

// Zeit in Sekunden bis auch im Normalmodus die Seite neu geladen wird
$refresh_zeit = 600;

// Systemnachrichten ausgeben
$sysmsg = TRUE;

$title = $body_titel;
zeige_header_anfang($title, 'chatausgabe');

// Userdaten gesetzt?
if ($u_id) {
	
	// Timestamp im Datensatz aktualisieren -> User im Chat / o_who=0
	aktualisiere_online($u_id, $o_raum, 0);
	
	// Algorithmus wählen
	if ($backup_chat || $u_backup) {
		// n-Zeilen ausgeben und nach Timeout neu laden
		?>
		<meta http-equiv="expires" content="0">
		<?php
		$meta_refresh = '<meta http-equiv="refresh" content="7; URL=chat.php?id=' . $id . '">';
		$meta_refresh .= "<script>\n"
			. "setInterval(\"window.scrollTo(1,300000)\",100)\n"
			. "function neuesFenster(url,name) {\n"
			. "hWnd=window.open(url,name,\"resizable=yes,scrollbars=yes,width=300,height=700\");\n"
			. "}\n</script>";
		
		zeige_header_ende($meta_refresh);

		// Chatausgabe, $letzte_id ist global
		chat_lese($o_id, $o_raum, $u_id, $sysmsg, $ignore, $chat_back);
	} else {
		$meta_refresh = "<meta http-equiv=\"expires\" content=\"0\">";
		$meta_refresh .= "<script language=JavaScript>\n"
			. "setInterval(\"window.scrollTo(1,300000)\",100)\n"
			. "function neuesFenster(url,name) {\n"
			. "hWnd=window.open(url,name,\"resizable=yes,scrollbars=yes,width=300,height=700\");\n"
			. "}\n" . "function neuesFenster2(url) {\n";
		$tmp = str_replace("-", "", $u_nick);
		$tmp = str_replace("+", "", $tmp);
		$tmp = str_replace("-", "", $tmp);
		$tmp = str_replace("ä", "", $tmp);
		$tmp = str_replace("ö", "", $tmp);
		$tmp = str_replace("ü", "", $tmp);
		$tmp = str_replace("Ä", "", $tmp);
		$tmp = str_replace("Ö", "", $tmp);
		$tmp = str_replace("Ü", "", $tmp);
		$tmp = str_replace("ß", "", $tmp);
		
		$meta_refresh.= "hWnd=window.open(url,\"640_$tmp\",\"resizable=yes,scrollbars=yes,width=780,height=700\");\n"
			. "}\n" . "</script>";
		
		zeige_header_ende($meta_refresh);
		
		// Voreinstellungen
		$j = 0;
		$i = 0;
		$beende_prozess = FALSE;
		set_time_limit($refresh_zeit + 30);
		ignore_user_abort(FALSE);
		
		// 1 Sek pro Durchlauf fest eingestellt
		if ($erweitertefeatures && FALSE) {
			// Für 0,2 Sek pro Durchlauf
			$durchlaeufe = $refresh_zeit * 5;
			$zeige_userliste = 500;
		} else {
			// Für 1 Sek pro Durchlauf
			$durchlaeufe = $refresh_zeit;
			$zeige_userliste = 100;
		}
		
		while ($j < ($durchlaeufe) && !$beende_prozess) {
			// Raum merken
			$o_raum_alt = $o_raum;
			
			// Bin ich noch online?
			$result = mysqli_query($mysqli_link, 
				"SELECT HIGH_PRIORITY o_raum,o_ignore FROM online WHERE o_id=$o_id ");
			if ($result > 0) {
				if (mysqli_num_rows($result) == 1) {
					$row = mysqli_fetch_object($result);
					$o_raum = $row->o_raum;
					$ignore = unserialize($row->o_ignore);
				} else {
					// Raus aus dem Chat, vorher kurz warten
					sleep(10);
					$beende_prozess = TRUE;
				}
				mysqli_free_result($result);
			}
			
			$j++;
			$i++;
			
			// Falls nach mehr als 100 sek. keine Ausgabe erfolgt, Userliste anzeigen
			// Nach > 120 Sekunden schlagen bei einigen Browsern Timeouts zu ;)
			if ($i > $zeige_userliste) {
				if (isset($raum_msg) && $raum_msg != "AUS") {
					system_msg("", 0, $u_id, $system_farbe, $raum_msg);
				} else {
					raum_user($o_raum, $u_id, "");
				}
				$i = 0;
			}
			
			// Raumwechsel?
			if (($back == 0) && ($o_raum != $o_raum_alt)) {
				// Trigger für die letzten Nachrichten setzen
				$back = 1;
			}
			
			// Chatausgabe, $letzte_id ist global
			// Falls Result=wahr wurde Text ausgegeben, Timer für Userliste zurücksetzen
			if (chat_lese($o_id, $o_raum, $u_id, $sysmsg, $ignore, $back)) {
				$i = 0;
			}
			
			// Trigger zurücksetzen
			$back = 0;
			
			// 0,2 oder 1 Sekunde warten
			if ($erweitertefeatures && FALSE) :
				usleep(200000);
			else :
				sleep(1);
			endif;
			
			// Ausloggen falls Browser abgebrochen hat
			if (connection_status() != 0) {
				// Verbindung wurde abgebrochen -> Schleife verlassen
				$beende_prozess = TRUE;
			}
		}
		
		// Trigger für die Ausgabe der letzten 20 Nachrichten setzen
		$back = 20;
		
		echo "<body onLoad='parent.chat.location=\"chat.php?id=$id&back=$back\"'>\n";
		flush();
	
	}
	
} else {
	zeige_header_ende();
	// Auf Chat-Eingangsseite leiten
	?>
	<body onLoad='javascript:parent.location.href="index.php'>
	<?php
}
?>
</body>
</html>