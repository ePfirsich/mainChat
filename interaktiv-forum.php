<?php

require_once("functions.php");
require_once("functions.php-func-raeume_auswahl.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, admin
id_lese($id);

// Prüfung, ob User wegen Inaktivität ausgelogt werden soll
if ($u_id && $chat_timeout && $u_level != 'S' && $u_level != 'C'
	&& $u_level != 'M' && $o_timeout_zeit) {
	
	if ($o_timeout_warnung == "J" && $chat_timeout < (time() - $o_timeout_zeit)) {
		
		// User ausloggen
		$zusatzjavascript = "<SCRIPT>\n"
			. "window.open(\"hilfe.php?http_host=$http_host&id=$id&aktion=logout\",'Logout',\"resizable=yes,scrollbars=yes,width=300,height=300\")\n"
			. "</SCRIPT>\n";
		require_once("functions.php-func-verlasse_chat.php");
		require_once("functions.php-func-nachricht.php");
		verlasse_chat($u_id, $u_nick, $o_raum);
		logout($o_id, $u_id, "forum->logout");
		unset($u_id);
		unset($o_id);
		
	} elseif ($o_timeout_warnung != "J"
		&& (($chat_timeout / 4) * 3) < (time() - $o_timeout_zeit)) {
		
		// Warnung über bevorstehenden Logout ausgeben
		system_msg("", 0, $u_id, $system_farbe,
			str_replace("%zeit%", $chat_timeout / 60, $t['chat_msg101']));
		unset($f);
		$f[o_timeout_warnung] = "J";
		schreibe_db("online", $f, $o_id, "o_id");
		
	}
} else {
	$zusatzjavascript = "";
}

if ($u_id) {
	// Kopf ausgeben
	
	// Fenstername
	$fenster = str_replace("+", "", $u_nick);
	$fenster = str_replace("-", "", $fenster);
	$fenster = str_replace("ä", "", $fenster);
	$fenster = str_replace("ö", "", $fenster);
	$fenster = str_replace("ü", "", $fenster);
	$fenster = str_replace("Ä", "", $fenster);
	$fenster = str_replace("Ö", "", $fenster);
	$fenster = str_replace("Ü", "", $fenster);
	$fenster = str_replace("ß", "", $fenster);
	
	
	$title = $body_titel;
	zeige_header_anfang($title, 'mini');
	?>
	<script>
	function neuesFenster(url) {
			hWnd=window.open(url,"<?php echo $fenster; ?>","resizable=yes,scrollbars=yes,width=300,height=580");
	}
	function neuesFenster2(url) {
			hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,width=780,height=580");
	}
	function neuesFenster3(url) {
			hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,menu=yes,width=780,height=580");
	}
	function window_reload(file,win_name) {
			win_name.location.href=file;
	}
	</script>
	<?php
	$meta_refresh = '<meta http-equiv="refresh" content="' . intval($timeout / 3) . '; URL=interaktiv-forum.php?http_host=' . $http_host . '&id=' . $id . '&o_raum_alt=' . $o_raum . '">';
	
	zeige_header_ende($meta_refresh);
	
	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum);
	
	// Aktionen ausführen, falls nicht innerhalb der letzten 5
	// Minuten geprüft wurde (letzte Prüfung=o_aktion)
	if ($communityfeatures && (time() > ($o_aktion + 300))) {
		aktion("Alle 5 Minuten", $u_id, $u_nick, $id);
	}
	
	// Menue Ausgeben:
	
	echo "<FORM ACTION=\"" . $chat_url
		. "index.php\" target=\"topframe\" NAME=\"form1\" METHOD=\"POST\">\n"
		. "<CENTER><TABLE BORDER=0 CELLSPACING=3 CELLPADDING=0>\n";
	
	// Anzahl der User insgesamt feststellen
	$query = "SELECT count(o_id) as anzahl FROM online "
		. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) != 0) {
		$anzahl_gesamt = mysqli_result($result, 0, "anzahl");
		mysqli_free_result($result);
	}
	
	echo "<tr><td ALIGN=\"CENTER\">";
	
	$mlnk[1] = "hilfe.php?http_host=$http_host&id=$id";
	$mlnk[2] = "raum.php?http_host=$http_host&id=$id";
	$mlnk[3] = "user.php?http_host=$http_host&id=$id";
	$mlnk[4] = "edit.php?http_host=$http_host&id=$id";
	$mlnk[5] = "index.php?http_host=$http_host&id=$id&aktion=logoff";
	$mlnk[6] = "hilfe.php?http_host=$http_host&id=$id&reset=1&forum=1";
	$mlnk[7] = "mail.php?http_host=$http_host&id=$id";
	$mlnk[8] = "log.php?http_host=$http_host&id=$id&back=500";
	$mlnk[10] = "edit.php?http_host=$http_host&id=$id";
	$mlnk[11] = "sperre.php?http_host=$http_host&id=$id";
	$mlnk[12] = "blacklist.php?http_host=$http_host&id=$id";
	$mlnk[13] = "forum-suche.php?http_host=$http_host&id=$id";
	
	echo $f1 . "<b>";
	echo "[<a href=\"$mlnk[1]\" target=\"640_$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mlnk[1]');return(false)\">$t[menue4]</a>]&nbsp;";
	echo "[<a href=\"$mlnk[3]\" target=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster('$mlnk[3]');return(false)\">$t[menue2]</a>]&nbsp;";
	
	if ($o_js)
		echo "[<a href=\"$mlnk[6]\" target=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster3('$mlnk[6]');return(false)\">$t[menue9]</a>]&nbsp;";
	
	echo "[<a href=\"$mlnk[8]\" target=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster3('$mlnk[8]');return(false)\">$t[menue7]</a>]&nbsp;";
	echo "[<a href=\"$mlnk[7]\" target=\"640_$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mlnk[7]');return(false)\">$t[menue10]</a>]&nbsp;";
	
	if ((isset($chat_logout_url)) && ($chat_logout_url)) {
		$logouttarget = "_top";
	} else {
		$logouttarget = "topframe";
	}
	
	if ($admin) {
		echo "<br>[<a href=\"$mlnk[5]\" onMouseOver=\"return(true)\" target=\"$logouttarget\">$t[menue6]</a>]&nbsp;"
			. "[<a href=\"$mlnk[4]\" target=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster('$mlnk[4]');return(false)\">$t[menue3]</a>]&nbsp;"
			. "[<a href=\"$mlnk[13]\" target=\"640_$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mlnk[13]');return(false)\">$t[menue13]</a>]<br>"
			. "[<a href=\"$mlnk[11]\" target=\"640_$fenster\" onClick=\"neuesFenster2('$mlnk[11]');return(false)\">$t[menue5]</a>]&nbsp;"
			. "[<a href=\"$mlnk[12]\" target=\"640_$fenster\" onClick=\"neuesFenster2('$mlnk[12]');return(false)\">$t[menue12]</a>]&nbsp;";
	} else {
		echo "<br>[<a href=\"$mlnk[4]\" target=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster('$mlnk[4]');return(false)\">$t[menue3]</a>]&nbsp;"
			. "&nbsp;&nbsp;&nbsp;[<a href=\"$mlnk[13]\" target=\"640_$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mlnk[13]');return(false)\">$t[menue13]</a>]&nbsp;"
			. "&nbsp;&nbsp;&nbsp;[<a href=\"$mlnk[5]\" onMouseOver=\"return(true)\" target=\"$logouttarget\">$t[menue6]</a>]&nbsp;";
	}
	echo "</b><br><br>\n";
	if (!(($u_level == 'U' || $level == 'G')
		&& (isset($useronline_anzeige_deaktivieren)
			&& $useronline_anzeige_deaktivieren == "1"))) {
		echo str_replace("%anzahl_gesamt%", $anzahl_gesamt,
			$t['forum_interaktiv_txt']) . $f2 . "<br>\n";
	}
	
	// Falls eintrittsraum nicht gesetzt ist, mit Lobby überschreiben
	if (strlen($eintrittsraum) == 0) {
		$eintrittsraum = $lobby;
	}
	
	$sql = "select r_id from raum where r_name like '" . mysqli_real_escape_string($mysqli_link, $eintrittsraum) . "'";
	$query = mysqli_query($mysqli_link, $sql);
	if (mysqli_num_rows($query) > 0)
		$lobby_id = mysqli_result($query, 0, "r_id");
	else $lobby_id = 1;
	
	echo $f3
		. "<NOBR><SELECT NAME=\"neuer_raum\"\" onChange=\"document.form1.submit()\">\n";
	
	// Admin sehen alle Räume, andere User nur die offenen
	if ($admin) {
		echo raeume_auswahl($lobby_id, TRUE, TRUE);
	} else {
		echo raeume_auswahl($lobby_id, FALSE, TRUE);
	}
	
	echo "</SELECT>";
	
	echo "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">"
		. "<INPUT TYPE=\"HIDDEN\" NAME=\"o_raum_alt\" VALUE=\"$o_raum\">"
		. "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
		. "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"relogin\">";
	
	echo " <INPUT TYPE=\"SUBMIT\" NAME=\"raum_submit\" VALUE=\"$t[button]\">&nbsp;</NOBR><br>"
		. $f4
		. "</td></tr><TR><TD ALIGN=\"CENTER\"><IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=2><br>\n";
	echo "</TD></TR></TABLE></CENTER></FORM>\n";
	
} else {
	// User wird nicht gefunden. Login ausgeben
	
	$title = $body_titel;
	zeige_header_anfang($title, 'login');

	zeige_header_ende();
	?>
	<body onLoad='javascript:parent.location.href="index.php?http_host=<?php echo $http_host; ?>'>
	<?php
}
?>
</body>
</html>