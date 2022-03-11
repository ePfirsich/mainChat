<?php
require_once("functions/functions.php");
require_once("functions/functions-interaktiv.php");
require_once("languages/$sprache-chat.php");
require_once("languages/$sprache-smilies.php");

$o_raum_alt = filter_input(INPUT_POST, 'o_raum_alt', FILTER_SANITIZE_NUMBER_INT);
$neuer_raum = filter_input(INPUT_POST, 'neuer_raum', FILTER_SANITIZE_NUMBER_INT);

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
id_lese();

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	header('Location: ' . $chat_url);
	exit();
	die;
}

// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");

// Der Refresh wird nur noch benötigt, um die Gesamtanzahl der Benutzer zu aktualisieren, wenn sich in anderen Räumen einloggen
$meta_refresh = "";
$meta_refresh .= '<meta http-equiv="refresh" content="30; URL=interaktiv.php?o_raum_alt=' . $o_raum . '">';
$meta_refresh .= "<script>\n function chat_reload(file) {\n parent.chat.location.href=file;\n}\n\n</script>\n";
$title = $body_titel;
zeige_header($title, $benutzerdaten['u_layout_farbe'], $meta_refresh);

echo "<body class=\"chatunten\">";

// Wurde Raum neuer_raum aus Formular übergeben? Falls ja Raum von $o_raum nach $neuer_raum wechseln
if (isset($neuer_raum) && $o_raum != $neuer_raum) {
	// Raum wechseln
	$o_raum = raum_gehe($o_id, $u_id, $u_nick, $o_raum, $neuer_raum);
	if ($o_raum == $neuer_raum) {
		// Benutzer in Raum ausgeben
		raum_user($neuer_raum, $u_id);
	}
	
	// Falls Pull-Chat, Chat-Fenster neu laden
//	echo "<script language=Javascript>"
//		. "chat_reload('chat.php')"
//		. "</script>\n";
}

// Daten für den Raum lesen
$query = pdoQuery("SELECT `r_id` FROM `raum`, `online` WHERE `r_id` = `o_raum` AND `o_id` = :o_id ORDER BY `o_aktiv` DESC", [':o_id'=>$o_id]);

$resultCount = $query->rowCount();
if ($resultCount != 0) {
	$result = $query->fetch();
	$o_raum = $result['r_id'];
}

if (!isset($o_raum_alt)) {
	$o_raum_alt = -9;
}

$text = "<div class=\"tabsy\">\n";

// Raumliste anzeigen
$text .= "<input type=\"radio\" id=\"tab1\" name=\"tab\" checked=\"checked\" >\n";
$text .= "<label class=\"tabButton\" for=\"tab1\">Räume</label>\n";
$text .= "<div class=\"tab\">\n";
$text .= "<div class=\"tab-content\">\n";


$text .= "<form action=\"interaktiv.php\" name=\"form1\" method=\"post\">\n";
$text .= "<table style=\"height:100%;\">\n";

// Anzahl der Benutzer insgesamt feststellen
$query = pdoQuery("SELECT COUNT(`o_id`) AS `anzahl` FROM `online` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) <= :o_aktiv", [':o_aktiv'=>$timeout]);

$resultCount = $query->rowCount();
if ($resultCount != 0) {
	$result = $query->fetch();
	$anzahl_gesamt = $result['anzahl'];
}

// Anzahl der Benutzer in diesem Raum feststellen
$query = pdoQuery("SELECT COUNT(`o_id`) AS `anzahl` FROM `online` WHERE `o_raum` = $o_raum AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) <= :o_aktiv", [':o_aktiv'=>$timeout]);

$resultCount = $query->rowCount();
if ($resultCount != 0) {
	$result = $query->fetch();
	$anzahl_raum = $result['anzahl'];
}

$text .= "<tr>\n";
$text .= "<td class=\"smaller\">\n";

if ($anzahl_raum == 1) {
	$txt = str_replace("%anzahl_raum%", $anzahl_raum, $lang['interaktiv1']);
} else {
	$txt = str_replace("%anzahl_raum%", $anzahl_raum, $lang['interaktiv2']);
}

$text .= " " . str_replace("%anzahl_gesamt%", $anzahl_gesamt, $txt) . "</td>";

$text .= "<td class=\"smaller\">\n";

// Special: Bei nur einem Raum keine Auswahl
$query = pdoQuery("SELECT COUNT(*) AS `zahl` FROM `raum`", []);

$a = $query->fetch();
$zahl = $a['zahl'];

if ($zahl > 1) {
	$text .= "$lang[interaktiv3]<br>\n";
	$text .= "<select name=\"neuer_raum\" onChange=\"document.form1.submit()\">\n";
	
	// Admin sehen alle Räume, andere Benutzer nur die offenen
	if ($admin) {
		$text .= raeume_auswahl($o_raum, TRUE, TRUE);
	} else {
		$text .= raeume_auswahl($o_raum, FALSE, TRUE);
	}
	$text .= "</select>\n";
	$text .= "<input type=\"hidden\" name=\"o_raum_alt\" value=\"$o_raum\">\n";
	$text .= "<input type=\"submit\" name=\"raum_submit\" value=\"Go!\"></td>\n";
}
$text .= "</tr>\n";
$text .= "</table>\n";
$text .= "</form>\n";


$text .= "</div>\n";
$text .= "</div>\n";

// Smilies anzeigen
$text .= "<input type=\"radio\" id=\"tab2\" name=\"tab\">\n";
$text .= "<label class=\"tabButton\" for=\"tab2\">Smilies</label>\n";
$text .= "<div class=\"tab\">\n";
$text .= "<div class=\"tab-content\" style=\"height: 68px; width: 100%; overflow-y: scroll;\">\n";

$text .= zeige_smilies('chat', $benutzerdaten);

$text .= "</div>\n";
$text .= "</div>\n";
$text .= "</div>\n";

zeige_tabelle_zentriert("", $text, true, false);
?>
</body>
</html>