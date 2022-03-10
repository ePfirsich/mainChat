<?php
require_once("functions/functions.php");
require_once("functions/functions-user.php");
require_once("functions/functions-nachrichten_betrete_verlasse.php");
require_once("languages/$sprache-user.php");

$aktion = filter_input(INPUT_GET, 'aktion', FILTER_SANITIZE_URL);

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, u_level, admin
id_lese($id);

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	header('Location: ' . $chat_url);
	exit();
	die;
}

// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");

$title = $body_titel . ' - Benutzer';
zeige_header($title, $benutzerdaten['u_layout_farbe'], "");

echo "<body>\n";


$text = "";

// Benutzerliste anzeigen


// Raum listen
$query = pdoQuery("SELECT `raum`.*, `o_user`, `o_name`, `o_ip`, `o_userdata`, `o_userdata2`, `o_userdata3`, `o_userdata4`, `r_besitzer` = `o_user` AS `isowner` "
	. "FROM `online` LEFT JOIN `raum` ON `o_raum` = `r_id` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) <= :timeout AND `r_id` = :r_id ORDER BY `o_name`", [':timeout'=>$timeout, ':r_id'=>$o_raum]);
$result = $query->fetchAll();
$larr = array();
foreach($result as $i => $row) {
	// Array mit Benutzerdaten und Infotexten aufbauen
	$userdata = unserialize( $row['o_userdata'] . $row['o_userdata2'] . $row['o_userdata3'] . $row['o_userdata4']);
	
	// Variable aus o_userdata setzen
	$larr[$i]['u_nick'] = strtr( str_replace("\\", "", htmlspecialchars($userdata['u_nick'])), "I", "i");
	$larr[$i]['u_level'] = $userdata['u_level'];
	$larr[$i]['u_id'] = $userdata['u_id'];
	$larr[$i]['u_away'] = $userdata['u_away'];
	$larr[$i]['u_punkte_anzeigen'] = $userdata['u_punkte_anzeigen'];
	$larr[$i]['u_punkte_gruppe'] = $userdata['u_punkte_gruppe'];
	$larr[$i]['u_punkte_gesamt'] = $userdata['u_punkte_gesamt'];
	$larr[$i]['u_chathomepage'] = $userdata['u_chathomepage'];
	$larr[$i]['r_besitzer'] = $row['r_besitzer'];
	$larr[$i]['r_topic'] = $row['r_topic'];
	$larr[$i]['o_ip'] = $row['o_ip'];
	$larr[$i]['isowner'] = $row['isowner'];
	
	if (!$row['r_name'] || $row['r_name'] == "NULL") {
		$larr[$i]['r_name'] = "[" . $whotext[($o_raum * (-1))] . "]";
	} else {
		$larr[$i]['r_name'] = $lang['benutzerliste_raum'] . $row['r_name'];
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

if (isset($larr)) {
	$rows = count($larr);
} else {
	$rows = 0;
}

$query = pdoQuery("SELECT * FROM `chat` WHERE `c_typ` = 'P' AND `c_von_user_id` = :c_von_user_id OR `c_typ` = 'P' AND `c_an_user` = :c_an_user", [':c_von_user_id'=>$u_id, ':c_an_user'=>$u_id]);
$pmu = $query->fetch();
$pmue = $query->rowCount();
if ($pmue > 0) {
	//Anfang Ausgabe PM Liste Rechts.
	$box2 = "<center>" . $lang['benutzerliste_private_nachrichten'] . "</center>";
	$text2 = "";
	
	$linkuser = "user.php";
	$text2 .= "<center>";
	
	// Benutzerliste rechte Seite ausgeben
	$text2 .= user_pm_list($larr);
	
	$text2 .= "</center>";
	
	zeige_tabelle_volle_breite($box2, $text2);
}

//Anfang Benutzerliste Ausgabe rechts
$text .= $larr[0]['r_name'] . "<br>\n";

// Benutzerliste rechte Seite ausgeben
$text .= user_liste($larr, true);

if ($rows > 15) {
	$text .= "<br><div style=\"text-align:center;\">";
	$text .= "</div>";
}

zeige_tabelle_volle_breite("<center>Infos</center>", $text);
?>
</body>
</html>