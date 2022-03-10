<?php
require_once("functions/functions.php");
require_once("functions/functions-interaktiv.php");
require_once("languages/$sprache-chat.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
id_lese($id);

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	header('Location: ' . $chat_url);
	exit();
	die;
}

// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");

$title = $body_titel;
zeige_header($title, $benutzerdaten['u_layout_farbe']);

echo "<body class=\"chatunten\">";

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

$text = "<form action=\"eingabe.php\" name=\"form1\" method=\"post\">\n";
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

echo $text;
?>
</body>
</html>