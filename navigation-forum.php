<?php
require_once("functions/functions.php");
require_once("functions/functions-func-raeume_auswahl.php");
require_once("languages/$sprache-navigation.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
id_lese($id);

$title = $body_titel;
zeige_header_anfang($title, 'mini', '', $u_layout_farbe);

$meta_refresh = "";
// Prüfung, ob Benutzer wegen Inaktivität ausgelogt werden soll
if ($u_id && $chat_timeout && $u_level != 'S' && $u_level != 'C' && $u_level != 'M' && $o_timeout_zeit) {
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

if ($u_id) {
	$meta_refresh .= '<meta http-equiv="refresh" content="' . intval($timeout / 3) . '; URL=navigation-forum.php?id=' . $id . '&o_raum_alt=' . $o_raum . '">';
	zeige_header_ende($meta_refresh);
	
	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum);
	
	// Aktionen ausführen, falls nicht innerhalb der letzten 5
	// Minuten geprüft wurde (letzte Prüfung=o_aktion)
	if ( time() > ($o_aktion + 300) ) {
		aktion("Alle 5 Minuten", $u_id, $u_nick, $id);
	}
	
	// Menue Ausgeben:
	
	// Anzahl der ungelesenen Nachrichten ermitteln
	$query_nachrichten = "SELECT mail.*,date_format(m_zeit,'%d.%m.%y um %H:%i') AS zeit,u_nick FROM mail LEFT JOIN user ON m_von_uid=u_id WHERE m_an_uid=$u_id  AND m_status='neu' ORDER BY m_zeit desc";
	$result_nachrichten = mysqli_query($mysqli_link, $query_nachrichten);
	
	if ($result_nachrichten && mysqli_num_rows($result_nachrichten) > 0) {
		$neue_nachrichten = " <span class=\"nachrichten_neu\">(".mysqli_num_rows($result_nachrichten).")</span>";
	} else {
		$neue_nachrichten = '';
	}
	
	$box = $t['menue1'];
	
	$text = "<center>";
	
	$text .= "<a href=\"inhalt.php?seite=benutzer&id=" . $id . "\" target=\"chat\" title=\"" . $t['menue4'] . "\"><span class=\"fa fa-user icon16\"></span> <span>" . $t['menue4'] . "</span></a>&nbsp;";
	$text .= " | <a href=\"inhalt.php?seite=nachrichten&id=" . $id . "\" target=\"chat\" title=\"" . $t['menue5'] . "\"><span class=\"fa fa-envelope icon16\"></span> <span>" . $t['menue5'] . $neue_nachrichten . "</span></a>&nbsp;";
	$text .= " | <a href=\"forum.php?id=" . $id . "\" target=\"chat\" title=\"" . $t['menue6'] . "\"><span class=\"fa fa-commenting icon16\"></span> <span>" . $t['menue6'] . "</span></a>&nbsp;";
	$text .= " | <a href=\"inhalt.php?seite=einstellungen&id=" . $id . "\" target=\"chat\" title=\"" . $t['menue7'] . "\"><span class=\"fa fa-cog icon16\"></span> <span>" . $t['menue7'] . "</span></a>&nbsp;";
	if ($admin) {
		$text .= " | <a href=\"inhalt.php?seite=sperren&id=" . $id . "\" target=\"chat\" title=\"" . $t['menue8'] . "\"><span class=\"fa fa-lock icon16\"></span> <span>" . $t['menue8'] . "</span></a>&nbsp;";
		$text .= " | <a href=\"inhalt.php?seite=blacklist&id=" . $id . "\" target=\"chat\" title=\"" . $t['menue9'] . "\"><span class=\"fa fa-list icon16\"></span> <span>" . $t['menue9'] . "</span></a>&nbsp;";
	}
	if ($admin) {
		$text .= " | <a href=\"inhalt.php?seite=statistik&id=" . $id . "\"\" target=\"chat\" title=\"" . $t['menue10'] . "\"><span class=\"fa fa-bar-chart icon16\"></span> <span>" . $t['menue10'] . "</span></a>&nbsp;";
	}
	if ($o_js) {
		$text .= " | <a href=\"reset.php?id=" . $id . "\"&forum=1\" onMouseOver=\"return(true)\" onClick=\"neuesFenster('reset.php?id=" . $id . "&reset=1&forum=1');return(false)\" title=\"" . $t['menue13'] . "\"><span class=\"fa fa-refresh icon16\"></span> <span>" . $t['menue13'] . "</span></a>&nbsp;";
	}
	$text .= " | <a href=\"inhalt.php?seite=hilfe&id=" . $id . "\" target=\"chat\" title=\"" . $t['menue14'] . "\"><span class=\"fa fa-question icon14\"></span> <span>" . $t['menue14'] . "</span></a>&nbsp;";
	$text .= " | &nbsp;<a href=\"index.php?id=" . $id . "&aktion=logoff\" target=\"_top\" title=\"" . $t['menue15'] . "\"><span class=\"fa fa-sign-out icon16\"></span> <span>" . $t['menue15'] . "</span></a>";
	
	$text .= "</center>";
	
	zeige_tabelle_zentriert($box, $text, true);
	
	
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
	
	if (!(($u_level == 'U' || $level == 'G') && (isset($useronline_anzeige_deaktivieren) && $useronline_anzeige_deaktivieren == "1"))) {
		if($anzahl_gesamt == 1) {
			echo $f1 . $t['forum_interaktiv_einzahl'] . $f2 . "&nbsp;";
		} else {
			echo $f1 . str_replace("%anzahl_gesamt%", $anzahl_gesamt, $t['forum_interaktiv_mehrzahl']) . $f2 . "&nbsp;";
		}
	}
	
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
	
	
} else {
	// Benutzer wird nicht gefunden. Login ausgeben
	zeige_header_ende($meta_refresh);
	?>
	<body onLoad='javascript:parent.location.href="index.php"'>
	<?php
}
?>
</body>
</html>