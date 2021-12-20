<?php
require_once("functions/functions.php");
require_once("functions/functions-interaktiv.php");
require_once("languages/$sprache-chat.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
id_lese($id);

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == "") {
	die;
}

// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");

// Der Refresh wird nur noch benötigt, um die Gesamtanzahl der Benutzer zu aktualisieren, wenn sie sich in anderen Räumen einloggen
$meta_refresh = "";
$meta_refresh .= '<meta http-equiv="refresh" content="30; URL=interaktiv.php?id=' . $id . '&o_raum_alt=' . $o_raum . '">';
$meta_refresh .= "<script>\n" . " function chat_reload(file) {\n" . "  parent.chat.location.href=file;\n}\n\n</script>\n";
$title = $body_titel;
zeige_header($title, $benutzerdaten['u_layout_farbe'], $meta_refresh);

echo "<body class=\"chatunten\">";

/*
// Aktionen ausführen, falls nicht innerhalb der letzten 5
// Minuten geprüft wurde (letzte Prüfung=o_aktion)
if ( time() > ($o_aktion + 300) ) {
	aktion($u_id, "Alle 5 Minuten", $u_id, $u_nick, $id);
}
*/

// Wurde Raum r_id aus Formular übergeben? Falls ja Raum von $o_raum nach $r_id wechseln
if (isset($r_id) && $o_raum != $r_id) {
	// Raum wechseln
	$o_raum = raum_gehe($o_id, $u_id, $u_nick, $o_raum, $r_id);
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
$query = "SELECT raum.* FROM raum,online WHERE r_id=o_raum AND o_id=$o_id ORDER BY o_aktiv DESC";

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
$text = "<form action=\"interaktiv.php\" name=\"form1\" method=\"post\">\n";
$text .= "<table style=\"height:100%;\">\n";
// Benutzer-Menue

// Anzahl der Benutzer insgesamt feststellen
$query = "SELECT COUNT(o_id) AS anzahl FROM online WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout";
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

$text .= "<tr>\n";
$text .= "<td class=\"smaller\">\n";

if ($anzahl_raum == 1) {
	$txt = str_replace("%anzahl_raum%", $anzahl_raum, $t['interaktiv1']);
} else {
	$txt = str_replace("%anzahl_raum%", $anzahl_raum, $t['interaktiv2']);
}

$text .= " " . str_replace("%anzahl_gesamt%", $anzahl_gesamt, $txt) . "</td>";

$text .= "<td class=\"smaller\">\n";

// Special: Bei nur einem Raum keine Auswahl
$query = "SELECT COUNT(*) AS zahl FROM raum";
$result = mysqli_query($mysqli_link, $query);
$a = mysqli_fetch_array($result, MYSQLI_ASSOC);
$zahl = $a['zahl'];

if ($zahl > 1) {
	$text .= "$t[interaktiv3]<br>\n";
	$text .= "<select name=\"r_id\" onChange=\"document.form1.submit()\">\n";
	
	// Admin sehen alle Räume, andere Benutzer nur die offenen
	if ($admin) {
		$text .= raeume_auswahl($o_raum, TRUE, TRUE);
	} else {
		$text .= raeume_auswahl($o_raum, FALSE, TRUE);
	}
	$text .= "</select>\n";
	$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	$text .= "<input type=\"hidden\" name=\"o_raum_alt\" value=\"$o_raum\">\n";
	$text .= "<input type=\"hidden\" name=\"neuer_raum\" value=\"1\">\n";
	$text .= "<input type=\"submit\" name=\"raum_submit\" value=\"Go!\"></td>\n";
}
$text .= "</tr>\n";
$text .= "</table>\n";
$text .= "</form>\n";

zeige_tabelle_zentriert_ohne_kopfzeile($text, true);
?>
</body>
</html>