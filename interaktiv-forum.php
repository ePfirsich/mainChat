<?php

require_once("functions.php");
require_once("functions.php-func-raeume_auswahl.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
id_lese($id);

// Prüfung, ob Benutzer wegen Inaktivität ausgelogt werden soll
if ($u_id && $chat_timeout && $u_level != 'S' && $u_level != 'C'
	&& $u_level != 'M' && $o_timeout_zeit) {
	
	if ($o_timeout_warnung == "J" && $chat_timeout < (time() - $o_timeout_zeit)) {
		
		// Benutzer ausloggen
		$zusatzjavascript = "<script>\n"
			. "window.open(\"logout.php?id=$id&aktion=logout\",'Logout',\"resizable=yes,scrollbars=yes,width=300,height=300\")\n"
			. "</script>\n";
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
	$meta_refresh = '<meta http-equiv="refresh" content="' . intval($timeout / 3) . '; URL=interaktiv-forum.php?id=' . $id . '&o_raum_alt=' . $o_raum . '">';
	
	zeige_header_ende($meta_refresh);
	
	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum);
	
	// Aktionen ausführen, falls nicht innerhalb der letzten 5
	// Minuten geprüft wurde (letzte Prüfung=o_aktion)
	if ($communityfeatures && (time() > ($o_aktion + 300))) {
		aktion("Alle 5 Minuten", $u_id, $u_nick, $id);
	}
	
	// Menue Ausgeben:
	
	echo "<form action=\"" . "index.php\" target=\"_top\" name=\"form1\" method=\"post\">\n" . "<center><table>\n";
	
	// Anzahl der Benutzer insgesamt feststellen
	$query = "SELECT count(o_id) as anzahl FROM online "
		. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) != 0) {
		$anzahl_gesamt = mysqli_result($result, 0, "anzahl");
		mysqli_free_result($result);
	}
	
	echo "<tr><td style=\"text-align:center;\">";
	
	$mlnk[1] = "index.php?id=$id&aktion=hilfe";
	$mlnk[2] = "raum.php?id=$id";
	$mlnk[3] = "user.php?id=$id";
	$mlnk[4] = "edit.php?id=$id";
	$mlnk[5] = "index.php?id=$id&aktion=logoff";
	$mlnk[6] = "logout.php?id=$id&reset=1&forum=1";
	$mlnk[7] = "mail.php?id=$id";
	$mlnk[8] = "log.php?id=$id&back=500";
	$mlnk[10] = "edit.php?id=$id";
	$mlnk[11] = "sperre.php?id=$id";
	$mlnk[12] = "blacklist.php?id=$id";
	$mlnk[13] = "forum-suche.php?id=$id";
	
	echo $f1;
	?>
	<a href="<?php echo $mlnk[1]; ?>" target="_blank" class="button" title="<?php echo $t['menue4']; ?>"><span class="fa fa-question icon16"></span> <span><?php echo $t['menue4']; ?></span></a>&nbsp;
	<a href="<?php echo $mlnk[3]; ?>" target="<?php echo $fenster; ?>" onMouseOver="return(true)" onClick="neuesFenster('<?php echo $mlnk[3]; ?>');return(false)" class="button" title="<?php echo $t['menue2']; ?>"><span class="fa fa-user icon16"></span> <span><?php echo $t['menue2']; ?></span></a>&nbsp;
	<?php
	
	if ($o_js) {
		?>
		<a href="<?php echo $mlnk[6]; ?>" target="<?php echo $fenster; ?>" onMouseOver="return(true)" onClick="neuesFenster3('<?php echo $mlnk[6]; ?>');return(false)" class="button" title="<?php echo $t['menue9']; ?>"><span class="fa fa-refresh icon16"></span> <span><?php echo $t['menue9']; ?></span></a>&nbsp;
		<?php
	}
	?>
	<a href="<?php echo $mlnk[8]; ?>" target="<?php echo $fenster; ?>" onMouseOver="return(true)" onClick="neuesFenster3('<?php echo $mlnk[8]; ?>');return(false)" class="button" title="<?php echo $t['menue7']; ?>"><span class="fa fa-archive icon16"></span> <span><?php echo $t['menue7']; ?></span></a>&nbsp;
	<a href="<?php echo $mlnk[7]; ?>" target="_blank" class="button" title="<?php echo $t['menue10']; ?>"><span class="fa fa-envelope icon16"></span> <span><?php echo $t['menue10']; ?></span></a>
	
	<br>
	<br>
	
	<a href="<?php echo $mlnk[4]; ?>" target="<?php echo $fenster; ?>" onMouseOver="return(true)" onClick="neuesFenster('<?php echo $mlnk[4]; ?>');return(false)" class="button" title="<?php echo $t['menue3']; ?>"><span class="fa fa-cog icon16"></span> <span><?php echo $t['menue3']; ?></span></a>&nbsp;
	<a href="<?php echo $mlnk[13]; ?>" target="640_<?php echo $fenster; ?>" onMouseOver="return(true)" onClick="neuesFenster2('<?php echo $mlnk[13]; ?>');return(false)" class="button" title="<?php echo $t['menue13']; ?>"><span class="fa fa-search icon16"></span> <span><?php echo $t['menue13']; ?></span></a>&nbsp;
	<?php
	if ($admin) {
		?>
		<a href="<?php echo $mlnk[11]; ?>" target="640_<?php echo $fenster; ?>" onMouseOver="return(true)" onClick="neuesFenster2('<?php echo $mlnk[11]; ?>');return(false)" class="button" title="<?php echo $t['menue5']; ?>"><span class="fa fa-lock icon16"></span> <span><?php echo $t['menue5']; ?></span></a>&nbsp;
		<a href="<?php echo $mlnk[12]; ?>" target="640_<?php echo $fenster; ?>" onMouseOver="return(true)" onClick="neuesFenster2('<?php echo $mlnk[12]; ?>');return(false)" class="button" title="<?php echo $t['menue12']; ?>"><span class="fa fa-list icon16"></span> <span><?php echo $t['menue12']; ?></span></a>&nbsp;
		<?php
	}
	?>
	<a href="<?php echo $mlnk[5]; ?>" onMouseOver="return(true)" target="_top" class="button" title="<?php echo $t['menue6']; ?>"><span class="fa fa-sign-out icon16"></span> <span><?php echo $t['menue6']; ?></span></a>
	<?php
	echo "<br><br>\n";
	if (!(($u_level == 'U' || $level == 'G') && (isset($useronline_anzeige_deaktivieren) && $useronline_anzeige_deaktivieren == "1"))) {
		echo str_replace("%anzahl_gesamt%", $anzahl_gesamt, $t['forum_interaktiv_txt']) . $f2 . "<br>\n";
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
	
	echo " <input type=\"submit\" name=\"raum_submit\" value=\"$t[button]\">&nbsp;</nobr><br>"
		. $f4
		. "</td></tr></table></center></form>\n";
	
} else {
	// Benutzer wird nicht gefunden. Login ausgeben
	
	$title = $body_titel;
	zeige_header_anfang($title, 'login');

	zeige_header_ende();
	?>
	<body onLoad='javascript:parent.location.href="index.php"'>
	<?php
}
?>
</body>
</html>