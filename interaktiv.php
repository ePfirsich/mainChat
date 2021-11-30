<?php
require_once("functions/functions.php");
require_once("languages/$sprache-chat.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
id_lese($id);

$title = $body_titel;
zeige_header_anfang($title, 'chatunten', '', $u_layout_farbe);
$meta_refresh = "";

if (isset($u_id) && $u_id) {
	$meta_refresh .= '<meta http-equiv="refresh" content="' . intval($timeout / 3) . '; URL=interaktiv.php?id=' . $id . '&o_raum_alt=' . $o_raum . '">';
	$meta_refresh .= "<script>\n" . " function chat_reload(file) {\n" . "  parent.chat.location.href=file;\n}\n\n</script>\n";
	zeige_header_ende($meta_refresh);
	?>
	<body>
	<?php
	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum);
	
	// Aktionen ausführen, falls nicht innerhalb der letzten 5
	// Minuten geprüft wurde (letzte Prüfung=o_aktion)
	if ( time() > ($o_aktion + 300) ) {
		aktion($u_id, "Alle 5 Minuten", $u_id, $u_nick, $id);
	}
	
	// Wurde Raum r_id aus Formular übergeben? Falls ja Raum von $o_raum nach $r_id wechseln
	if (isset($r_id) && $o_raum != $r_id) {
		// Raum wechseln
		$o_raum = raum_gehe($o_id, $u_id, $u_nick, $o_raum, $r_id, 0);
		if ($o_raum == $r_id) {
			// Benutzer in Raum ausgeben
			raum_user($r_id, $u_id);
		}
		
		// Falls Pull-Chat, Chat-Fenster neu laden
		echo "<script language=Javascript>"
			. "chat_reload('chat.php?id=$id')"
			. "</script>\n";
	}
	
	// Daten für Raum lesen
	$query = "SELECT raum.* FROM raum,online WHERE r_id=o_raum "
		. "AND o_id=$o_id ORDER BY o_aktiv DESC";
	
	$result = mysqli_query($mysqli_link, $query);
	
	if ($result && mysqli_num_rows($result) != 0) {
		$row = mysqli_fetch_object($result);
		$o_raum = $row->r_id;
	}
	mysqli_free_result($result);
	
	if (!isset($o_raum_alt)) {
		$o_raum_alt = -9;
	}
	
	// Menue Ausgeben:
	?>
	<form action="interaktiv.php" name="form1" method="post">
	<table style="height:100%;">
	<?php
	// Benutzer-Menue
	
	// Anzahl der Benutzer insgesamt feststellen
	$query = "SELECT count(o_id) as anzahl FROM online WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) != 0) {
		$anzahl_gesamt = mysqli_result($result, 0, "anzahl");
		mysqli_free_result($result);
	}
	
	// Anzahl der Benutzer in diesem Raum feststellen
	$query = "SELECT COUNT(o_id) AS anzahl FROM online WHERE o_raum=$o_raum AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) != 0) {
		$anzahl_raum = mysqli_result($result, 0, "anzahl");
		mysqli_free_result($result);
	}
	
	if ($u_level != "M") {
		?>
		<tr>
		<td>&nbsp;</td>
		<td>
		<?php
	} else {
		?>
		<tr>
		<td>
		<?php
	}
	echo $f1;
	
	if ($anzahl_raum == 1) {
		$txt = str_replace("%anzahl_raum%", $anzahl_raum, $t['interaktiv1']);
	} else {
		$txt = str_replace("%anzahl_raum%", $anzahl_raum, $t['interaktiv2']);
	}
	if (!(($u_level == 'U' || $level == 'G')
		&& (isset($useronline_anzeige_deaktivieren)
			&& $useronline_anzeige_deaktivieren == "1"))) {
		echo " " . str_replace("%anzahl_gesamt%", $anzahl_gesamt, $txt) . $f2
			. "</td>";
	}
	
	if ($u_level != "M") {
		?>
		<td></td>
		<td>
		<?php
	} else {
		?>
		</td>
		</tr>
		<tr>
		<td>
		<?php
	}
	
	// Special: Bei nur einem Raum kein Auswahl
	$query = "SELECT count(*) as zahl FROM raum";
	$result = mysqli_query($mysqli_link, $query);
	$a = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$zahl = $a['zahl'];
	
	if (($u_level == "U" || $u_level == "G") && ($single_room_verhalten == "1"))
		$zahl = 0;
	
	if ($zahl > 1) {
		?>
		<?php echo $f3; ?>&nbsp;&nbsp;<?php echo $t['interaktiv3']; ?><br>&nbsp;<nobr>
		<select name="r_id" onChange="document.form1.submit()">
		<?php
		// Admin sehen alle Räume, andere Benutzer nur die offenen
		if ($admin) {
			echo raeume_auswahl($o_raum, TRUE, TRUE);
		} else {
			echo raeume_auswahl($o_raum, FALSE, TRUE);
		}
		?>
		</select>
		<input type="hidden" name="id" value="<?php echo $id; ?>">
		<input type="hidden" name="o_raum_alt" value="<?php echo $o_raum; ?>">
		<input type="hidden" name="neuer_raum" value="1">
		<?php
		if ($u_level != "M") {
			echo "<input type=\"submit\" name=\"raum_submit\" value=\"Go!\">&nbsp;</nobr>"
				. $f4 . "</TD>\n";
		} else {
			echo "<br>&nbsp;"
				. "<input type=\"submit\" name=\"raum_submit\" value=\"Go!\">&nbsp;</nobr>"
				. $f4 . "</td>\n" . "</tr><tr>";
		}
	}
	?>
	<td></td>
	</tr>
	</table>
	</form>

	<?php
} else {
	// Benutzer wird nicht gefunden. Login ausgeben
	zeige_header_ende($meta_refresh);
	?>
	<body onLoad='javascript:parent.location.href="index.php'>
	<?php
	exit;
}
?>
</body>
</html>