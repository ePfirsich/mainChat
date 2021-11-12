<?php

require_once("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
id_lese($id);

// Prüfung, ob Benutzer wegen Inaktivität ausgelogt werden soll
if ($u_id && $chat_timeout && $u_level != 'S' && $u_level != 'C' && $u_level != 'M' && $o_timeout_zeit) {
	if ($o_timeout_warnung == "J" && $chat_timeout < (time() - $o_timeout_zeit)) {
		
		// Benutzer ausloggen
		$zusatzjavascript = "<script>\n"
			. "window.open(\"logout.php?id=$id&aktion=logout\",'Logout',\"resizable=yes,scrollbars=yes,width=300,height=300\")\n"
			. "</script>\n";
		require_once("functions-func-verlasse_chat.php");
		require_once("functions-func-nachricht.php");
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
	$title = $body_titel;
	zeige_header_anfang($title, 'chatunten');
	$meta_refresh = '<meta http-equiv="refresh" content="' . intval($timeout / 3) . '; URL=navigation-forum.php?id=' . $id . '">';
	zeige_header_ende($meta_refresh);
	
	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum);
	
	// Aktionen ausführen, falls nicht innerhalb der letzten 5
	// Minuten geprüft wurde (letzte Prüfung=o_aktion)
	if (time() > ($o_aktion + 300) ) {
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
	
	echo "<center><table>\n";
	echo "<tr><td style=\"text-align:center;\"><b>";
	echo $f1;
	?>
	<a href="user.php?id=<?php echo $id; ?>" target="chat" class="button" title="<?php echo $t['menue2']; ?>"><span class="fa fa-user icon16"></span> <span><?php echo $t['menue2']; ?></span></a>&nbsp;
	<a href="mail.php?id=<?php echo $id; ?>" target="chat" class="button" title="<?php echo $t['menue10']; ?>"><span class="fa fa-envelope icon16"></span> <span><?php echo $t['menue10'] . $neue_nachrichten; ?></span></a>&nbsp;
	<a href="edit.php?id=<?php echo $id; ?>" target="chat" class="button" title="<?php echo $t['menue3']; ?>"><span class="fa fa-cog icon16"></span> <span><?php echo $t['menue3']; ?></span></a>&nbsp;
	<?php
	if ($admin) {
		?>
		<a href="sperre.php?id=<?php echo $id; ?>" target="chat" class="button" title="<?php echo $t['menue5']; ?>"><span class="fa fa-lock icon16"></span> <span><?php echo $t['menue5']; ?></span></a>&nbsp;
		<a href="blacklist.php?id=<?php echo $id; ?>" target="chat" class="button" title="<?php echo $t['menue12']; ?>"><span class="fa fa-list icon16"></span> <span><?php echo $t['menue12']; ?></span></a>&nbsp;
		<?php
	}
	if ($admin) {
		?>
		<a href="user.php?id=<?php echo $id; ?>&aktion=statistik" target="chat" class="button" title="<?php echo $t['menue13']; ?>"><span class="fa fa-bar-chart icon16"></span> <span><?php echo $t['menue13']; ?></span></a>&nbsp;
		<?php
	}
	if ($o_js) {
		?>
		<a href="logout.php?id=<?php echo $id; ?>&reset=1&forum=1" onMouseOver="return(true)" onClick="neuesFenster('logout.php?id=<?php echo $id; ?>&reset=1&forum=1');return(false)" class="button" title="<?php echo $t['menue9']; ?>"><span class="fa fa-refresh icon16"></span> <span><?php echo $t['menue9']; ?></span></a>&nbsp;
		<?php
	}
	?>
	<a href="hilfe.php?id=<?php echo $id; ?>" target="chat" class="button" title="<?php echo $t['menue4']; ?>"><span class="fa fa-question icon16"></span> <span><?php echo $t['menue4']; ?></span></a>&nbsp;
	&nbsp;
	<a href="index.php?id=<?php echo $id; ?>&aktion=logoff" target="_top" class="button" title="<?php echo $t['menue6']; ?>"><span class="fa fa-sign-out icon16"></span> <span><?php echo $t['menue6']; ?></span></a>
	<?php
	echo $f2;
	echo "</b></td></tr></table></center>";
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