<?php
require_once("functions/functions.php");
require_once("languages/$sprache-navigation.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
id_lese($id);

$title = $body_titel;
zeige_header_anfang($title, 'mini', '', $u_layout_farbe);

if ($u_id) {
	// Ermitteln, ob sich der Benutzer im Chat oder im Forum aufhält
	if ($o_raum && $o_raum == "-1") {
		$wo_online = "forum";
		$reset = "1";
	} else {
		$wo_online = "chat";
		$reset = "0";
	}
	
	// Weitere Funktionen für die Raumanzeige im Forum notwendig
	if($wo_online == "forum") {
		require_once("functions/functions-func-raeume_auswahl.php");
	}
	
	// Prüfung, ob Benutzer wegen Inaktivität ausgeloggt werden soll
	if ($chat_timeout && $u_level != 'S' && $u_level != 'C' && $u_level != 'M' && $o_timeout_zeit) {
		if ($o_timeout_warnung == "J" && $chat_timeout < (time() - $o_timeout_zeit)) {
			// Aus dem Chat ausloggen
			ausloggen($u_id, $u_nick, $o_raum, $o_id);
			unset($u_id);
			unset($o_id);
		} else if ($o_timeout_warnung != "J" && (($chat_timeout / 4) * 3) < (time() - $o_timeout_zeit)) {
			// Warnung über bevorstehenden Logout ausgeben
			system_msg("", 0, $u_id, $system_farbe, str_replace("%zeit%", $chat_timeout / 60, $t['chat_msg101']));
			unset($f);
			$f[o_timeout_warnung] = "J";
			schreibe_db("online", $f, $o_id, "o_id");
		}
	}

	$meta_refresh = '<meta http-equiv="refresh" content="' . intval($timeout / 3) . '; URL=navigation.php?id=' . $id . '">';
	zeige_header_ende($meta_refresh);
	echo "<body>\n";
	
	// Das wird nur im Forum benötigt, da es im Chat momentan noch in anderen Frames (schreibe.php, etc) durchgeführt wird
	if($wo_online == "forum") {
		// Timestamp im Datensatz aktualisieren
		aktualisiere_online($u_id, $o_raum);
	
		// Aktionen ausführen, falls nicht innerhalb der letzten 5
		// Minuten geprüft wurde (letzte Prüfung=o_aktion)
		if ( time() > ($o_aktion + 300) ) {
			aktion($u_id, "Alle 5 Minuten", $u_id, $u_nick, $id);
		}
	}
	
	// Anzahl der ungelesenen Nachrichten ermitteln
	$query_nachrichten = "SELECT mail.*,date_format(m_zeit,'%d.%m.%y um %H:%i') AS zeit,u_nick FROM mail LEFT JOIN user ON m_von_uid=u_id WHERE m_an_uid=$u_id  AND m_status='neu' ORDER BY m_zeit desc";
	$result_nachrichten = mysqli_query($mysqli_link, $query_nachrichten);
	
	if ($result_nachrichten && mysqli_num_rows($result_nachrichten) > 0) {
		$neue_nachrichten = " <span class=\"nachrichten_neu\">(".mysqli_num_rows($result_nachrichten).")</span>";
	} else {
		$neue_nachrichten = '';
	}
	
	$box = $t['navigation_menue1'];
	
	$text = "<center>";
	if($wo_online == "chat") {
		$text .= "<a href=\"chat.php?id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue2'] . "\"><span class=\"fa fa-commenting icon16\"></span> <span>" . $t['navigation_menue2'] . "</span></a>&nbsp;";
		$text .= " | <a href=\"inhalt.php?seite=raum&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue3'] . "\"><span class=\"fa fa-road icon16\"></span> <span>" . $t['navigation_menue3'] . "</span></a>&nbsp;|&nbsp;";
	}
	$text .= "<a href=\"inhalt.php?seite=benutzer&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue4'] . "\"><span class=\"fa fa-user icon16\"></span> <span>" . $t['navigation_menue4'] . "</span></a>&nbsp;";
	if ($u_level != 'G') {
		$text .= " | <a href=\"inhalt.php?seite=nachrichten&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue5'] . "\"><span class=\"fa fa-envelope icon16\"></span> <span>" . $t['navigation_menue5'] . $neue_nachrichten . "</span></a>&nbsp;";
	}
	if($wo_online == "forum") {
		$text .= " | <a href=\"forum.php?id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue6'] . "\"><span class=\"fa fa-commenting icon16\"></span> <span>" . $t['navigation_menue6'] . "</span></a>&nbsp;";
	} else if ($wo_online == "chat" && $forumfeatures) {
		$text .= " | <a href=\"index-forum.php?id=" . $id . "\" onMouseOver=\"return(true)\" target=\"_top\" title=\"" . $t['navigation_menue6'] . "\"><span class=\"fa fa-commenting icon16\"></span> <span>" . $t['navigation_menue6'] . "</span></a>&nbsp;";
	}
	$text .= " | <a href=\"inhalt.php?seite=einstellungen&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue7'] . "\"><span class=\"fa fa-cog icon16\"></span> <span>" . $t['navigation_menue7'] . "</span></a>&nbsp;";
	$text .= " | <a href=\"inhalt.php?seite=profil&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue9'] . "\"><span class=\"fa fa-user-circle-o icon16\"></span> <span>" . $t['navigation_menue9'] . "</span></a>&nbsp;";
	if ($u_level != 'G') {
		$text .= " | <a href=\"inhalt.php?seite=freunde&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue16'] . "\"><span class=\"fa fa-users icon16\"></span> <span>" . $t['navigation_menue16'] . "</span></a>&nbsp;";
	}
	if ($admin) {
		$text .= " | <a href=\"inhalt.php?seite=sperren&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue8'] . "\"><span class=\"fa fa-lock icon16\"></span> <span>" . $t['navigation_menue8'] . "</span></a>&nbsp;";
		$text .= " | <a href=\"inhalt.php?seite=statistik&id=" . $id . "\"\" target=\"chat\" title=\"" . $t['navigation_menue10'] . "\"><span class=\"fa fa-bar-chart icon16\"></span> <span>" . $t['navigation_menue10'] . "</span></a>&nbsp;";
	}
	
	if ($wo_online == "chat" && $u_level == "M") {
			$text .= " | <a href=\"moderator.php?id=" . $id . "&mode=answer\" target=\"chat\" title=\"" . $t['navigation_menue11'] . "\"><span class=\"fa fa-reply icon16\"></span> <span>" . $t['navigation_menue11'] . "</span></a>&nbsp;";
	}
	if ($wo_online == "chat") {
		$text .= " | <a href=\"inhalt.php?seite=log&id=" . $id . "&back=500\" target=\"_blank\" title=\"" . $t['navigation_menue12'] . "\"><span class=\"fa fa-archive icon16\"></span> <span>" . $t['navigation_menue12'] . "</span></a>&nbsp;";
	}
	$text .= " | <a href=\"schreibe.php?id=$id&aktion=reset\"  target=\"schreibe\" title=\"" . $t['navigation_menue13'] . "\"><span class=\"fa fa-refresh icon16\"></span> <span>" . $t['navigation_menue13'] . "</span></a>&nbsp;";
	$text .= " | <a href=\"inhalt.php?seite=hilfe&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue14'] . "\"><span class=\"fa fa-question icon16\"></span> <span>" . $t['navigation_menue14'] . "</span></a>&nbsp;";
	$text .= " | <a href=\"index.php?id=" . $id . "&aktion=logoff\" target=\"_top\" title=\"" . $t['navigation_menue15'] . "\"><span class=\"fa fa-sign-out icon16\"></span> <span>" . $t['navigation_menue15'] . "</span></a>";
	
	$text .= "</center>";
	
	zeige_tabelle_zentriert($box, $text, true);
	
	// Die direkte Raumauswahl wird nur im Forum angezeigt
	if($wo_online == "forum") {
		// Chat und Raumauswahl anzeigen
		echo "<form action=\"" . "index.php\" target=\"_top\" name=\"form1\" method=\"post\">\n" . "<center><table style=\"margin-top: 7px;\">\n";
		
		// Anzahl der Benutzer insgesamt feststellen
		$query = "SELECT COUNT(o_id) AS anzahl FROM online WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) != 0) {
			$anzahl_gesamt = mysqli_result($result, 0, "anzahl");
			mysqli_free_result($result);
		}
		
		echo "<tr><td style=\"text-align:center;\">";
		
		// Falls eintrittsraum nicht gesetzt ist, mit Lobby überschreiben
		if (strlen($eintrittsraum) == 0) {
			$eintrittsraum = $lobby;
		}
		
		$sql = "SELECT r_id FROM raum WHERE r_name LIKE '" . mysqli_real_escape_string($mysqli_link, $eintrittsraum) . "'";
		$query = mysqli_query($mysqli_link, $sql);
		if (mysqli_num_rows($query) > 0) {
			$lobby_id = mysqli_result($query, 0, "r_id");
		} else {
			$lobby_id = 1;
		}
		
		echo $f3 . "<nobr><select name=\"neuer_raum\" onChange=\"document.form1.submit()\">\n";
		
		// Admin sehen alle Räume, andere Benutzer nur die offenen
		if ($admin) {
			echo raeume_auswahl($lobby_id, TRUE, TRUE);
		} else {
			echo raeume_auswahl($lobby_id, FALSE, TRUE);
		}
		
		echo "</select>";
		
		echo "<input type=\"hidden\" name=\"id\" value=\"$id\">"
			. "<input type=\"hidden\" name=\"o_raum_alt\" value=\"$o_raum\">"
			. "<input type=\"hidden\" name=\"aktion\" value=\"relogin\">";
		echo " <input type=\"submit\" name=\"raum_submit\" value=\"$t[zum_chat]\">&nbsp;</nobr><br>" . $f4 . "</td></tr></table></center></form>\n";
	}
} else {
	// Benutzer wird nicht gefunden. Login ausgeben
	zeige_header_ende();
	echo "<body onLoad='parent.location.href=\"index.php\"'>\n";
}
?>
</body>
</html>