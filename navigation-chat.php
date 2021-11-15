<?php

require_once("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
id_lese($id);

if ($u_id) {
	// Default für Farbe setzen, falls undefiniert
	if (!isset($u_farbe)) {
		$u_farbe = $user_farbe;
	}

$title = $body_titel;
zeige_header_anfang($title, 'mini', '', $u_layout_farbe);
?>
<script>
function resetinput() {
	document.forms['form'].elements['text'].value=document.forms['form'].elements['text2'].value;
<?php
	if ($u_clearedit == 1) {
		echo "	document.forms['form'].elements['text2'].value='';\n";
	}
?>
	document.forms['form'].submit();
	document.forms['form'].elements['text2'].focus();
	document.forms['form'].elements['text2'].select();
}
</script>
<?php
$meta_refresh = '<meta http-equiv="refresh" content="' . intval($timeout / 3) . '; URL=navigation-chat.php?id=' . $id . '">';
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
	
	$box = $t['menue15'];
	
	$text = "<center>";
	
	if (!isset($beichtstuhl) || !$beichtstuhl || $admin) {
		$text .= "<a href=\"chat.php?id=" . $id . "\" target=\"chat\" title=\"" . $t['menue1'] . "\"><span class=\"fa fa-commenting icon16\"></span> <span>" . $t['menue14'] . "</span></a>&nbsp;";
		$text .= " | <a href=\"inhalt.php?seite=raum&id=" . $id . "\" target=\"chat\" title=\"" . $t['menue1'] . "\"><span class=\"fa fa-road icon16\"></span> <span>" . $t['menue1'] . "</span></a>&nbsp;";
		$text .= " | <a href=\"user.php?id=" . $id . "\" target=\"chat\" title=\"" . $t['menue2'] . "\"><span class=\"fa fa-user icon16\"></span> <span>" . $t['menue2'] . "</span></a>&nbsp;";
	}
	if ($u_level != 'G' && (!isset($beichtstuhl) || !$beichtstuhl || $admin)) {
		$text .= " | <a href=\"inhalt.php?seite=nachrichten&id=" . $id . "\" target=\"chat\" title=\"" . $t['menue10'] . "\"><span class=\"fa fa-envelope icon16\"></span> <span>" . $t['menue10'] . $neue_nachrichten . "</span></a>&nbsp;";
	}
	if ($forumfeatures && (!isset($beichtstuhl) || !$beichtstuhl || $admin) && ((isset($aktraum) && $aktraum->r_status1 <> "L") || $admin)) {
		$text .= " | <a href=\"index-forum.php?id=" . $id . "\" onMouseOver=\"return(true)\" target=\"_top\" title=\"" . $t['menue11'] . "\"><span class=\"fa fa-commenting icon16\"></span> <span>" . $t['menue11'] . "</span></a>&nbsp;";
	}
	if (!isset($beichtstuhl) || !$beichtstuhl || $admin) {
		$text .= " | <a href=\"inhalt.php?seite=einstellungen&id=" . $id . "\" target=\"chat\" title=\"" . $t['menue3'] . "\"><span class=\"fa fa-cog icon16\"></span> <span>" . $t['menue3'] . "</span></a>&nbsp;";
	}
	if ($admin) {
		$text .= " | <a href=\"inhalt.php?seite=sperren&id=" . $id . "\" target=\"chat\" title=\"" . $t['menue5'] . "\"><span class=\"fa fa-lock icon16\"></span> <span>" . $t['menue5'] . "</span></a>&nbsp;";
		$text .= " | <a href=\"inhalt.php?seite=blacklist&id=" . $id . "\" target=\"chat\" title=\"" . $t['menue12'] . "\"><span class=\"fa fa-list icon16\"></span> <span>" . $t['menue12'] . "</span></a>&nbsp;";
		$text .= " | <a href=\"inhalt.php?seite=statistik&id=" . $id . "\" target=\"chat\" title=\"" . $t['menue13'] . "\"><span class=\"fa fa-bar-chart icon16\"></span> <span>" . $t['menue13'] . "</span></a>&nbsp;";
	}
	if ($u_level == "M") {
		$text .= " | <a href=\"moderator.php?id=" . $id . "&mode=answer\" onClick=\"neuesFenster('" . $mlnk[8] . "');return(false)\" title=\"" . $t['menue8'] . "\"><span class=\"fa fa-reply icon16\"></span> <span>" . $t['menue8'] . "</span></a>&nbsp;";
	}
	$text .= " | <a href=\"log.php?id=" . $id . "&back=500\" target=\"_blank\" title=\"" . $t['menue7'] . "\"><span class=\"fa fa-archive icon16\"></span> <span>" . $t['menue7'] . "</span></a>&nbsp;";
	if ($o_js) {
		$text .= " | <a href=\"logout.php?id=" . $id . "&reset=1\" onMouseOver=\"return(true)\" onClick=\"neuesFenster('logout.php?id=" . $id . "&reset=1');return(false)\" title=\"" . $t['menue9'] . "\"><span class=\"fa fa-refresh icon16\"></span> <span>" . $t['menue9'] . "</span></a>&nbsp;";
	}
	$text .= " | <a href=\"inhalt.php?seite=hilfe&id=" . $id . "\" target=\"chat\" title=\"" . $t['menue4'] . "\"><span class=\"fa fa-question icon16\"></span> <span>" . $t['menue4'] . "</span></a>&nbsp;";
	$text .= " | <a href=\"index.php?id=" . $id . "&aktion=logoff\" target=\"_top\" title=\"" . $t['menue6'] . "\"><span class=\"fa fa-sign-out icon16\"></span> <span>" . $t['menue6'] . "</span></a>";
	
	$text .= "</center>";
	
	zeige_tabelle_zentriert($box, $text, true);
	
	unset($aktraum);
} else {
	echo "<vody onLoad='parent.location.href=\"index.php\"'>\n";
}

?>
</body>
</html>