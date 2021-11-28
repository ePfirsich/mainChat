<?php

require_once("functions/functions.php");
require_once("functions/functions-func-nachricht.php");
require_once("languages/$sprache-user.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, u_level, admin
id_lese($id);

if (!isset($suchtext)) {
	$suchtext = "";
}
$uu_suchtext = URLENCODE($suchtext);

// welcher raum soll abgefragt werden? ggf voreinstellen
// Positives $schau_raum entspricht r_id
// Negatives $schau_raum entspricht o_who * -1

if (isset($schau_raum) && !is_numeric($schau_raum)) {
	unset($schau_raum);
}

if ((isset($schau_raum)) && $schau_raum < 0) {
	// Community-Bereich gewählt
	$raum_subquery = "AND o_who=" . ($schau_raum * (-1));
} elseif ((isset($schau_raum)) && $schau_raum > 0) {
	// Raum gewählt
	$raum_subquery = "AND r_id=$schau_raum";
} elseif (!isset($schau_raum) && $o_who != 0) {
	// Voreinstellung auf den aktuellen Community-Bereich
	$schau_raum = $o_who * (-1);
	$raum_subquery = "AND o_who=$o_who";
} elseif (!isset($schau_raum) || $schau_raum == 0) {
	// Voreinstellung auf den aktuellen Raum
	$schau_raum = $o_raum;
	$raum_subquery = "AND r_id=$schau_raum";
}

$title = $body_titel . ' - Benutzer';
zeige_header_anfang($title, 'mini', '', $u_layout_farbe);

// Target Sonderzeichen raus...
$fenster = str_replace("+", "", $u_nick);
$fenster = str_replace("-", "", $fenster);
$fenster = str_replace("ä", "", $fenster);
$fenster = str_replace("ö", "", $fenster);
$fenster = str_replace("ü", "", $fenster);
$fenster = str_replace("Ä", "", $fenster);
$fenster = str_replace("Ö", "", $fenster);
$fenster = str_replace("Ü", "", $fenster);
$fenster = str_replace("ß", "", $fenster);

echo "<script>\n" . "  var id='$id';\n"
. "  var u_nick='" . $fenster . "';\n" . "  var raum='$schau_raum';\n";
if (($admin) || ($u_level == 'M')) {
	echo "  var inaktiv_ansprechen='0';\n"
		. "  var inaktiv_mailsymbol='0';\n"
		. "  var inaktiv_userfunktionen='0';\n";
} else {
	echo "  var inaktiv_ansprechen='$userleiste_inaktiv_ansprechen';\n"
	. "  var inaktiv_mailsymbol='$userleiste_inaktiv_mailsymbol';\n"
	. "  var inaktiv_userfunktionen='$userleiste_inaktiv_userfunktionen';\n";
}

echo "  var stdparm='?id='+id+'&schau_raum='+raum;\n"
	. "  var stdparm2='?id='+id;\n";
	?>
	</script>
	<?php
$meta_refresh = '<meta http-equiv="refresh" content="' . intval($timeout / 3) . '; URL=user.php?id=' . $id . '">';
zeige_header_ende($meta_refresh);

// Login ok?
if (strlen($u_id) != 0) {
	echo "<body>\n";
	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum);
		
	// Raum listen
	$query = "SELECT raum.*,o_user,o_name,o_ip,o_userdata,o_userdata2,o_userdata3,o_userdata4,r_besitzer=o_user AS isowner "
		. "FROM online LEFT JOIN raum ON o_raum=r_id "
		. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout $raum_subquery "
		. "ORDER BY o_name";
	
	$result = mysqli_query($mysqli_link, $query);
	
	for ($i = 0; $row = mysqli_fetch_array($result, MYSQLI_ASSOC); $i++) {
		// Array mit Benutzerdaten und Infotexten aufbauen
		$userdata = unserialize( $row['o_userdata'] . $row['o_userdata2'] . $row['o_userdata3'] . $row['o_userdata4']);
		
		// Variable aus o_userdata setzen
		$larr[$i]['u_email'] = str_replace("\\", "", htmlspecialchars($userdata['u_email']));
		$larr[$i]['u_nick'] = strtr( str_replace("\\", "", htmlspecialchars($userdata['u_nick'])), "I", "i");
		$larr[$i]['u_level'] = $userdata['u_level'];
		$larr[$i]['u_id'] = $userdata['u_id'];
		$larr[$i]['u_away'] = $userdata['u_away'];
		$larr[$i]['u_punkte_anzeigen'] = $userdata['u_punkte_anzeigen'];
		$larr[$i]['u_punkte_gruppe'] = $userdata['u_punkte_gruppe'];
		$larr[$i]['u_chathomepage'] = $userdata['u_chathomepage'];
		$larr[$i]['r_besitzer'] = $row['r_besitzer'];
		$larr[$i]['r_topic'] = $row['r_topic'];
		$larr[$i]['o_ip'] = $row['o_ip'];
		$larr[$i]['isowner'] = $row['isowner'];
		
		if (!$row['r_name'] || $row['r_name'] == "NULL") {
			$larr[$i]['r_name'] = "[" . $whotext[($schau_raum * (-1))] . "]";
		} else {
			$larr[$i]['r_name'] = $t['sonst42'] . $row['r_name'];
		}

		// Spezialbehandlung für Admins
		if (!$admin) {
			$larr[$i]['o_ip'] = "";
		}
		
		// Raumbesitzer einstellen, falls Level=Benutzer
		if ($larr[$i]['isowner'] && $userdata['u_level'] == "U") {
			$larr[$i]['u_level'] = "B";
		}
		
	}
	mysqli_free_result($result);
	
	if (isset($larr)) {
		$rows = count($larr);
	} else {
		$rows = 0;
	}
	
	// Fehlerbehandlung, falls Arry leer ist -> nichts gefunden
	if (!$rows && $schau_raum > 0) {
		
		$query = "SELECT r_name FROM raum WHERE r_id=" . intval($schau_raum);
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) != 0)
			$r_name = mysqli_result($result, 0, 0);
		echo "<p>" . str_replace("%r_name%", $r_name, $t['sonst13'])
			. "</p>\n";
		
	} elseif (!$rows && $schau_raum < 0) {
		
		echo "<p>"
			. str_replace("%whotext%", $whotext[($schau_raum * (-1))],
				$t['sonst43']) . "</p>\n";
		
	} else { // array ist gefüllt -> Daten ausgeben
		$pmu = mysqli_query($mysqli_link, "SELECT * FROM chat WHERE c_typ='P' AND c_von_user_id=".$u_id." OR c_typ='P' AND c_an_user=".$u_id);
		$pmue = mysqli_num_rows($pmu);
		if ($pmue > 0) {
			//Anfang Ausgabe PM Liste Rechts.
			$box = "<center>" . $t['sonst55'] . "</center>";
			$text = "";
		
			$linkuser = "user.php?id=$id";
			$text .= "<center>";
		
			// Benutzerliste rechte Seite ausgeben
			$text .= user_pm_list($larr);
			
			$text .= "</center>";
			
			zeige_tabelle_volle_breite($box, $text);
		}
		
		//Anfang Benutzerliste Ausgabe rechts
		$box = "<center>" . $t['sonst1'] . "</center>";
		$text = "";
		
		$linkuser = "user.php?id=$id";
		$linksmilies = "smilies.php?id=$id";
		$text .= "<center>";
		$text .= "<a href=\"$linkuser\"><span class=\"fa fa-refresh icon16\"></span>" . $t['sonst19'] . "</a>";
		$text .= " | <a href=\"$linksmilies\"><span class=\"fa fa-smile-o icon16\"></span>" . $t['sonst20'] . "</a>";
		$text .= "<br><br>\n" . $larr[0]['r_name'] . "<br>\n";
		
		// Benutzerliste rechte Seite ausgeben
		$text .= user_liste($larr, true);
	
		if ($rows > 15) {
			$text .= "<a href=\"$linkuser\"><span class=\"fa fa-refresh icon16\"></span>" . $t['sonst19'] . "</a>";
			$text .= " | <a href=\"$linksmilies\"><span class=\"fa fa-smile-o icon16\"></span>" . $t['sonst20'] . "</a>";
			$text .= "</center>\n";
		}
		
		zeige_tabelle_volle_breite($box, $text);
	}
} else {
	?>
	<body onLoad='javascript:parent.location.href="index.php"'>
	<p style="text-align: center;"><?php echo $t['ausgeloggt']; ?></p>
	<?php
}
?>
</body>
</html>