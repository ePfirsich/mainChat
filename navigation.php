<?php
require_once("functions/functions.php");
require_once("languages/$sprache-navigation.php");

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_URL);

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
id_lese($id);

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");

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
	if ($o_timeout_warnung == 1 && $chat_timeout < (time() - $o_timeout_zeit)) {
		// Aus dem Chat ausloggen
		ausloggen($u_id, $u_nick, $o_raum, $o_id);
		unset($u_id);
		unset($o_id);
	} else if ($o_timeout_warnung != 1 && (($chat_timeout / 4) * 3) < (time() - $o_timeout_zeit)) {
		// Warnung über bevorstehenden Logout ausgeben
		system_msg("", 0, $u_id, $system_farbe, str_replace("%zeit%", $chat_timeout / 60, $t['chat_msg101']));
		unset($f);
		$f[o_timeout_warnung] = 1;
		schreibe_db("online", $f, $o_id, "o_id");
	}
}

if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

$meta_refresh = '<meta http-equiv="refresh" content="10; URL=navigation.php?id=' . $id . '">';
$title = $body_titel;
zeige_header($title, $benutzerdaten['u_layout_farbe'], $meta_refresh);
echo "<body>\n";

// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id);

// Aktionen ausführen, falls nicht innerhalb der letzten 5
// Minuten geprüft wurde (letzte Prüfung=o_aktion)
if ( time() > ($o_aktion + 300) ) {
	aktion($u_id, "Alle 5 Minuten", $u_id, $u_nick, $id);
}

// Anzahl der ungelesenen Nachrichten ermitteln
$query_nachrichten = "SELECT mail.*, `u_nick` FROM `mail` LEFT JOIN `user` ON `m_von_uid` = `u_id` WHERE `m_an_uid` = $u_id AND `m_status` = 'neu' ORDER BY `m_zeit` DESC";
$result_nachrichten = sqlQuery($query_nachrichten);

if ($result_nachrichten && mysqli_num_rows($result_nachrichten) > 0) {
	$neue_nachrichten = " <span class=\"nachrichten_neu\">(".mysqli_num_rows($result_nachrichten).")</span>";
} else {
	$neue_nachrichten = '';
}

$text = "<center>";
if($wo_online == "chat") {
	$text .= "<a href=\"chat.php?id=$id\" target=\"chat\" title=\"" . $t['navigation_chat'] . "\"><span class=\"fa fa-commenting icon16\"></span> <span>" . $t['navigation_chat'] . "</span></a>&nbsp;";
	$text .= " | <a href=\"inhalt.php?bereich=raum&id=$id\" target=\"chat\" title=\"" . $t['navigation_raeume'] . "\"><span class=\"fa fa-road icon16\"></span> <span>" . $t['navigation_raeume'] . "</span></a>&nbsp;|&nbsp;";
}
$text .= "<a href=\"inhalt.php?bereich=benutzer&id=$id\" target=\"chat\" title=\"" . $t['navigation_benutzer'] . "\"><span class=\"fa fa-user icon16\"></span> <span>" . $t['navigation_benutzer'] . "</span></a>&nbsp;";
if ($u_level != 'G') {
	$text .= " | <a href=\"inhalt.php?bereich=nachrichten&id=$id\" target=\"chat\" title=\"" . $t['navigation_nachrichten'] . "\"><span class=\"fa fa-envelope icon16\"></span> <span>" . $t['navigation_nachrichten'] . $neue_nachrichten . "</span></a>&nbsp;";
}
if($wo_online == "forum") {
	$text .= " | <a href=\"forum.php?id=$id\" target=\"chat\" title=\"" . $t['navigation_forum'] . "\"><span class=\"fa fa-commenting icon16\"></span> <span>" . $t['navigation_forum'] . "</span></a>&nbsp;";
} else if ($wo_online == "chat" && $forumfeatures) {
	$text .= " | <a href=\"index-forum.php?id=$id\" onMouseOver=\"return(true)\" target=\"_top\" title=\"" . $t['navigation_forum'] . "\"><span class=\"fa fa-commenting icon16\"></span> <span>" . $t['navigation_forum'] . "</span></a>&nbsp;";
}
$text .= " | <a href=\"inhalt.php?bereich=einstellungen&id=$id\" target=\"chat\" title=\"" . $t['navigation_einstellungen'] . "\"><span class=\"fa fa-cog icon16\"></span> <span>" . $t['navigation_einstellungen'] . "</span></a>&nbsp;";
if ($u_level != 'G') {
	$text .= " | <a href=\"inhalt.php?bereich=profil&id=$id\" target=\"chat\" title=\"" . $t['navigation_profil'] . "\"><span class=\"fa fa-user-circle-o icon16\"></span> <span>" . $t['navigation_profil'] . "</span></a>&nbsp;";
	$text .= " | <a href=\"inhalt.php?bereich=freunde&id=$id\" target=\"chat\" title=\"" . $t['navigation_freunde'] . "\"><span class=\"fa fa-users icon16\"></span> <span>" . $t['navigation_freunde'] . "</span></a>&nbsp;";
}
if ($admin) {
	$text .= " | <a href=\"inhalt.php?bereich=sperren&id=$id\" target=\"chat\" title=\"" . $t['navigation_sperren'] . "\"><span class=\"fa fa-lock icon16\"></span> <span>" . $t['navigation_sperren'] . "</span></a>&nbsp;";
	$text .= " | <a href=\"inhalt.php?bereich=statistik&id=$id\" target=\"chat\" title=\"" . $t['navigation_statistik'] . "\"><span class=\"fa fa-bar-chart icon16\"></span> <span>" . $t['navigation_statistik'] . "</span></a>&nbsp;";
}

if ($wo_online == "chat" && $u_level == "M") {
		$text .= " | <a href=\"moderator.php?id=" . $id . "&mode=answer\" target=\"chat\" title=\"" . $t['navigation_definierte_antworten'] . "\"><span class=\"fa fa-reply icon16\"></span> <span>" . $t['navigation_definierte_antworten'] . "</span></a>&nbsp;";
}
if ($wo_online == "chat") {
	$text .= " | <a href=\"inhalt.php?bereich=log&id=$id\" target=\"_blank\" title=\"" . $t['navigation_log'] . "\"><span class=\"fa fa-archive icon16\"></span> <span>" . $t['navigation_log'] . "</span></a>&nbsp;";
}
$text .= " | <a href=\"inhalt.php?bereich=hilfe&id=$id\" target=\"chat\" title=\"" . $t['navigation_hilfe'] . "\"><span class=\"fa fa-question icon16\"></span> <span>" . $t['navigation_hilfe'] . "</span></a>&nbsp;";
$text .= " | <a href=\"index.php?id=$id&bereich=logoff\" target=\"_top\" title=\"" . $t['navigation_logout'] . "\"><span class=\"fa fa-sign-out icon16\"></span> <span>" . $t['navigation_logout'] . "</span></a>";

$text .= "</center>";

zeige_tabelle_zentriert($chat, $text, true);

// Die direkte Raumauswahl wird nur im Forum angezeigt
if($wo_online == "forum") {
	// Chat und Raumauswahl anzeigen
	echo "<form action=\"index.php?bereich=relogin\" target=\"_top\" name=\"form1\" method=\"post\">\n" . "<div style=\"margin-top: 7px; text-align:center;\" class=\"smaller\">\n";
	
	// Anzahl der Benutzer insgesamt feststellen
	$query = "SELECT COUNT(o_id) AS anzahl FROM online WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout";
	$result = sqlQuery($query);
	if ($result && mysqli_num_rows($result) != 0) {
		$anzahl_gesamt = mysqli_result($result, 0, "anzahl");
		mysqli_free_result($result);
	}
	
	if($anzahl_gesamt == 1) {
		echo $t['forum_interaktiv_einzahl'] . "&nbsp;";
	} else {
		echo str_replace("%anzahl_gesamt%", $anzahl_gesamt, $t['forum_interaktiv_mehrzahl']) . "&nbsp;";
	}
	
	// Falls eintrittsraum nicht gesetzt ist, mit Lobby überschreiben
	if (strlen($eintrittsraum) == 0) {
		$eintrittsraum = $lobby;
	}
	
	$sql = "SELECT r_id FROM raum WHERE r_name LIKE '" . escape_string($eintrittsraum) . "'";
	$query = sqlQuery($sql);
	if (mysqli_num_rows($query) > 0) {
		$lobby_id = mysqli_result($query, 0, "r_id");
	} else {
		$lobby_id = 1;
	}
	
	echo "<nobr><select name=\"neuer_raum\" onChange=\"document.form1.submit()\">\n";
	
	// Admin sehen alle Räume, andere Benutzer nur die offenen
	if ($admin) {
		echo raeume_auswahl($lobby_id, TRUE, TRUE);
	} else {
		echo raeume_auswahl($lobby_id, FALSE, TRUE);
	}
	
	echo "</select>\n";
	
	echo "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	echo "<input type=\"hidden\" name=\"o_raum_alt\" value=\"$o_raum\">\n";
	echo "<input type=\"submit\" name=\"raum_submit\" value=\"$t[zum_chat]\">&nbsp;</nobr>\n";
	echo "</div>\n";
	echo "</form>\n";
}
?>
</body>
</html>