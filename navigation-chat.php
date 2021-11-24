<?php
require_once("functions/functions.php");

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
	$meta_refresh .= '<meta http-equiv="refresh" content="' . intval($timeout / 3) . '; URL=navigation-chat.php?id=' . $id . '">';
	zeige_header_ende($meta_refresh);
	?>
	<body>
	<?php
	// Anzahl der ungelesenen Nachrichten ermitteln
	$query_nachrichten = "SELECT mail.*,date_format(m_zeit,'%d.%m.%y um %H:%i') AS zeit,u_nick FROM mail LEFT JOIN user ON m_von_uid=u_id WHERE m_an_uid=$u_id  AND m_status='neu' ORDER BY m_zeit desc";
	$result_nachrichten = mysqli_query($mysqli_link, $query_nachrichten);
	
	if ($result_nachrichten && mysqli_num_rows($result_nachrichten) > 0) {
		$neue_nachrichten = " <span class=\"nachrichten_neu\">(".mysqli_num_rows($result_nachrichten).")</span>";
	} else {
		$neue_nachrichten = '';
	}
	
	if ($forumfeatures) {
		// Raumstatus lesen, für Temporär, damit FORUM nicht angezeigt wird
		
		$query = "SELECT r_status1 from raum WHERE r_id=" . intval($o_raum);
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$aktraum = mysqli_fetch_object($result);
			mysqli_free_result($result);
		}
	}
	
	$box = $t['navigation_menue1'];
	
	$text = "<center>";
	
	if (!isset($beichtstuhl) || !$beichtstuhl || $admin) {
		$text .= "<a href=\"chat.php?id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue2'] . "\"><span class=\"fa fa-commenting icon16\"></span> <span>" . $t['navigation_menue2'] . "</span></a>&nbsp;";
		$text .= " | <a href=\"inhalt.php?seite=raum&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue3'] . "\"><span class=\"fa fa-road icon16\"></span> <span>" . $t['navigation_menue3'] . "</span></a>&nbsp;";
		$text .= " | <a href=\"inhalt.php?seite=benutzer&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue4'] . "\"><span class=\"fa fa-user icon16\"></span> <span>" . $t['navigation_menue4'] . "</span></a>&nbsp;";
	}
	if ($u_level != 'G' && (!isset($beichtstuhl) || !$beichtstuhl || $admin)) {
		$text .= " | <a href=\"inhalt.php?seite=nachrichten&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue5'] . "\"><span class=\"fa fa-envelope icon16\"></span> <span>" . $t['navigation_menue5'] . $neue_nachrichten . "</span></a>&nbsp;";
	}
	if ($forumfeatures && (!isset($beichtstuhl) || !$beichtstuhl || $admin) && ((isset($aktraum) && $aktraum->r_status1 <> "L") || $admin)) {
		$text .= " | <a href=\"index-forum.php?id=" . $id . "\" onMouseOver=\"return(true)\" target=\"_top\" title=\"" . $t['navigation_menue6'] . "\"><span class=\"fa fa-commenting icon16\"></span> <span>" . $t['navigation_menue6'] . "</span></a>&nbsp;";
	}
	if (!isset($beichtstuhl) || !$beichtstuhl || $admin) {
		$text .= " | <a href=\"inhalt.php?seite=einstellungen&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue7'] . "\"><span class=\"fa fa-cog icon16\"></span> <span>" . $t['navigation_menue7'] . "</span></a>&nbsp;";
		$text .= " | <a href=\"inhalt.php?seite=profil&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue9'] . "\"><span class=\"fa fa-user-circle-o icon16\"></span> <span>" . $t['navigation_menue9'] . "</span></a>&nbsp;";
	}
	if ($u_level != 'G') {
		$text .= " | <a href=\"inhalt.php?seite=freunde&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue16'] . "\"><span class=\"fa fa-users icon16\"></span> <span>" . $t['navigation_menue16'] . "</span></a>&nbsp;";
	}
	if ($admin) {
		$text .= " | <a href=\"inhalt.php?seite=sperren&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue8'] . "\"><span class=\"fa fa-lock icon16\"></span> <span>" . $t['navigation_menue8'] . "</span></a>&nbsp;";
		$text .= " | <a href=\"inhalt.php?seite=statistik&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue10'] . "\"><span class=\"fa fa-bar-chart icon16\"></span> <span>" . $t['navigation_menue10'] . "</span></a>&nbsp;";
	}
	if ($u_level == "M") {
		$text .= " | <a href=\"moderator.php?id=" . $id . "&mode=answer\" target=\"chat\" title=\"" . $t['navigation_menue11'] . "\"><span class=\"fa fa-reply icon16\"></span> <span>" . $t['navigation_menue11'] . "</span></a>&nbsp;";
	}
	$text .= " | <a href=\"inhalt.php?seite=log&id=" . $id . "&back=500\" target=\"_blank\" title=\"" . $t['navigation_menue12'] . "\"><span class=\"fa fa-archive icon16\"></span> <span>" . $t['navigation_menue12'] . "</span></a>&nbsp;";
	if ($o_js) {
		$text .= " | <a href=\"reset.php?id=" . $id . "\" onMouseOver=\"return(true)\" onClick=\"neuesFenster('reset.php?id=" . $id . "&reset=1');return(false)\" title=\"" . $t['navigation_menue13'] . "\"><span class=\"fa fa-refresh icon16\"></span> <span>" . $t['navigation_menue13'] . "</span></a>&nbsp;";
	}
	$text .= " | <a href=\"inhalt.php?seite=hilfe&id=" . $id . "\" target=\"chat\" title=\"" . $t['navigation_menue14'] . "\"><span class=\"fa fa-question icon16\"></span> <span>" . $t['navigation_menue14'] . "</span></a>&nbsp;";
	$text .= " | <a href=\"index.php?id=" . $id . "&aktion=logoff\" target=\"_top\" title=\"" . $t['navigation_menue15'] . "\"><span class=\"fa fa-sign-out icon16\"></span> <span>" . $t['navigation_menue15'] . "</span></a>";
	
	$text .= "</center>";
	
	zeige_tabelle_zentriert($box, $text, true);
	
	unset($aktraum);
} else {
	echo "<vody onLoad='parent.location.href=\"index.php\"'>\n";
}

?>
</body>
</html>